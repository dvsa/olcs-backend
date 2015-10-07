<?php

/**
 * Cpms Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @deprecated to be replaced eventually by CpmsV2HelperService
 */
namespace Dvsa\Olcs\Api\Service;

use CpmsClient\Service\ApiService;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cpms Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsV1HelperService implements FactoryInterface, CpmsHelperInterface
{
    // CPMS' preferred date format (note: this changed around 03/2015)
    const DATE_FORMAT = 'Y-m-d';

    const PRODUCT_REFERENCE = 'GVR_APPLICATION_FEE';

    const COST_CENTRE = '12345,67890';

    protected $logger;

    protected $cpmsClient;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->cpmsClient = $serviceLocator->get('cpms\service\api');
        $this->logger = $serviceLocator->get('Logger');
        return $this;
    }

    protected function getClient()
    {
        return $this->cpmsClient;
    }

    /**
     * Initiate a card payment
     *
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array $fees
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function initiateCardRequest(
        $redirectUrl,
        array $fees
    ) {
        $totalAmount = $this->getTotalAmountFromFees($fees);

        $paymentData = [];
        foreach ($fees as $fee) {
            $paymentData[] = [
                'amount' => $this->formatAmount($fee->getAmount()),
                'sales_reference' => (string)$fee->getId(),
                'product_reference' => self::PRODUCT_REFERENCE,
                'payment_reference' => [
                    'rule_start_date' => $this->formatDate($fee->getRuleStartDate()),
                ],
            ];
        }

        $endPoint = '/api/payment/card';
        $scope    = ApiService::SCOPE_CARD;

        $params = [
            // @NOTE CPMS rejects ints as 'missing', so we have to force a string...
            'customer_reference' => (string) $this->getCustomerReference($fees),
            'scope' => $scope,
            'disable_redirection' => true,
            'redirect_uri' => $redirectUrl,
            'payment_data' => $paymentData,
            'cost_centre' => self::COST_CENTRE,
            'total_amount' => $this->formatAmount($totalAmount),
        ];

        $this->debug(
            'Card payment request',
            [
                'method' => [
                    'location' => __METHOD__,
                    'data' => func_get_args()
                ],
                'endPoint' => $endPoint,
                'scope'    => $scope,
                'params'   => $params,
            ]
        );

        $response = $this->getClient()->post($endPoint, $scope, $params);

        $this->debug('Card payment response', ['response' => $response]);
        if (!is_array($response)
            || !isset($response['receipt_reference'])
            || empty($response['receipt_reference'])
        ) {
            throw new CpmsResponseException('Invalid payment response: '.json_encode($response));
        }

        return $response;
    }

    /**
     * Update CPMS with payment result
     *
     * @param string $reference payment reference / guid
     * @param array $data response data from the payment gateway
     * @return array|mixed response
     * @see CpmsClient\Service\ApiService::put()
     */
    public function handleResponse($reference, $data)
    {
        /**
         * Let CPMS know the response from the payment gateway
         *
         * We have to bundle up the response data verbatim as it can
         * vary per gateway implementation
         */
        return $this->getClient()->put('/api/gateway/' . $reference . '/complete', ApiService::SCOPE_CARD, $data);
    }

    /**
     * Determine the status of a payment/transaction
     *
     * @param string $receiptReference
     * @return int status code
     */
    public function getPaymentStatus($receiptReference)
    {
        $endPoint = '/api/payment/'.$receiptReference;
        $scope = ApiService::SCOPE_QUERY_TXN;
        $params = [
            'required_fields' => [
                'payment' => [
                    'payment_status'
                ]
            ]
        ];

        $this->debug(
            'Payment status request',
            [
                'method' => [
                    'location' => __METHOD__,
                    'data' => func_get_args()
                ],
                'endPoint' => $endPoint,
                'scope'    => $scope,
            ]
        );

        $response = $this->getClient()->get($endPoint, $scope, $params);

        $this->debug('Payment status response', ['response' => $response]);

        return $response['payment_status']['code'];
    }

    /**
     * Record a cash payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param string|DateTime $receiptDate
     * @param string $payer payer name
     * @param string $slipNo paying in slip number
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function recordCashPayment(
        $fees,
        $amount,
        $receiptDate,
        $payer,
        $slipNo
    ) {
        $paymentData = [];
        foreach ($fees as $fee) {
            $paymentData[] = [
                'amount' => $this->formatAmount($fee->getAmount()),
                'sales_reference' => (string)$fee->getId(),
                'product_reference' => self::PRODUCT_REFERENCE,
                'payer_details' => $payer,
                'payment_reference' => [
                    'rule_start_date' => $this->formatDate($fee->getRuleStartDate()),
                    'receipt_date' => $this->formatDate($receiptDate),
                    'slip_number' => (string)$slipNo,
                ],
            ];
        }

        $endPoint = '/api/payment/cash';
        $scope    = ApiService::SCOPE_CASH;

        $params = [
            'customer_reference' => (string) $this->getCustomerReference($fees),
            'scope' => $scope,
            'total_amount' => $this->formatAmount($amount),
            'payment_data' => $paymentData,
            'cost_centre' => self::COST_CENTRE,
        ];

        $this->debug(
            'Cash payment request',
            [
                'method' => [
                    'location' => __METHOD__,
                    'data' => func_get_args()
                ],
                'endPoint' => $endPoint,
                'scope'    => $scope,
                'params'   => $params,
            ]
        );

        $response = $this->getClient()->post($endPoint, $scope, $params);

        $this->debug('Cash payment response', ['response' => $response]);

        if ($this->isSuccessfulPaymentResponse($response)) {
            return $response;
        }

        throw new CpmsResponseException('Invalid payment response: '.json_encode($response));
    }

    /**
     * Record a cheque payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param array $receiptDate (from DateSelect)
     * @param string $payer payer name
     * @param string $slipNo paying in slip number
     * @param string $chequeNo cheque number
     * @param string $chequeDate (from DateSelect)
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function recordChequePayment(
        $fees,
        $amount,
        $receiptDate,
        $payer,
        $slipNo,
        $chequeNo,
        $chequeDate
    ) {
        $paymentData = [];
        foreach ($fees as $fee) {
            $paymentData[] = [
                'amount' => $this->formatAmount($fee->getAmount()),
                'sales_reference' => (string)$fee->getId(),
                'product_reference' => self::PRODUCT_REFERENCE,
                'payer_details' => $payer,
                'payment_reference' => [
                    'rule_start_date' => $this->formatDate($fee->getRuleStartDate()),
                    'receipt_date' => $this->formatDate($receiptDate),
                    'cheque_number' => (string)$chequeNo,
                    'cheque_date' => $this->formatDate($chequeDate),
                    'slip_number' => (string)$slipNo,
                ],
            ];
        }

        $endPoint = '/api/payment/cheque';
        $scope    = ApiService::SCOPE_CHEQUE;

        $params = [
            'customer_reference' => (string) $this->getCustomerReference($fees),
            'scope' => $scope,
            'total_amount' => $this->formatAmount($amount),
            'payment_data' => $paymentData,
            'cost_centre' => self::COST_CENTRE,
        ];

        $this->debug(
            'Cheque payment request',
            [
                'method' => [
                    'location' => __METHOD__,
                    'data' => func_get_args()
                ],
                'endPoint' => $endPoint,
                'scope'    => $scope,
                'params'   => $params,
            ]
        );

        $response = $this->getClient()->post($endPoint, $scope, $params);

        $this->debug('Cheque payment response', ['response' => $response]);

        if ($this->isSuccessfulPaymentResponse($response)) {
            return $response;
        }

        throw new CpmsResponseException('Invalid payment response: '.json_encode($response));
    }

    /**
     * Record a Postal Order payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param array $receiptDate (from DateSelect)
     * @param string $payer payer name
     * @param string $slipNo paying in slip number
     * @param string $poNo Postal Order number
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function recordPostalOrderPayment(
        $fees,
        $amount,
        $receiptDate,
        $payer,
        $slipNo,
        $poNo
    ) {
        $paymentData = [];
        foreach ($fees as $fee) {
            $paymentData[] = [
                'amount' => $this->formatAmount($fee->getAmount()),
                'sales_reference' => (string)$fee->getid(),
                'product_reference' => self::PRODUCT_REFERENCE,
                'payer_details' => $payer,
                'payment_reference' => [
                    'rule_start_date' => $this->formatDate($fee->getRuleStartDate()),
                    'receipt_date' => $this->formatDate($receiptDate),
                    'postal_order_number' => [ $poNo ], // array!
                    'slip_number' => (string)$slipNo,
                ],
            ];
        }

        $endPoint = '/api/payment/postal-order';
        $scope    = ApiService::SCOPE_POSTAL_ORDER;

        $params = [
            'customer_reference' => (string) $this->getCustomerReference($fees),
            'scope' => $scope,
            'total_amount' => $this->formatAmount($amount),
            'payment_data' => $paymentData,
            'cost_centre' => self::COST_CENTRE,
        ];

        $this->debug(
            'Postal order payment request',
            [
                'method' => [
                    'location' => __METHOD__,
                    'data' => func_get_args()
                ],
                'endPoint' => $endPoint,
                'scope'    => $scope,
                'params'   => $params,
            ]
        );

        $response = $this->getClient()->post($endPoint, $scope, $params);

        $this->debug('Postal order payment response', ['response' => $response]);

        if ($this->isSuccessfulPaymentResponse($response)) {
            return $response;
        }

        throw new CpmsResponseException('Invalid payment response: '.json_encode($response));
    }

    /**
     * Get a list of available reports
     *
     * @return array
     * @codeCoverageIgnore deprecated class will be removed
     */
    public function getReportList()
    {
        throw new \Exception(sprintf("%s not implemented in version %d", __METHOD__, $this->getVersion()));
    }

    /**
     * Request report creation
     *
     * @param string $reportCode
     * @param DateTime $start
     * @param DateTime $end
     * @return array
     * @inheritdoc
     * @codeCoverageIgnore deprecated class will be removed
     */
    public function requestReport($reportCode, \DateTime $start, \DateTime $end)
    {
        throw new \Exception(sprintf("%s not implemented in version %d", __METHOD__, $this->getVersion()));
    }

    /**
     * Check report status by reference
     *
     * @param string $reference
     * @return array
     * @inheritdoc
     * @codeCoverageIgnore deprecated class will be removed
     */
    public function getReportStatus($reference)
    {
        throw new \Exception(sprintf("%s not implemented in version %d", __METHOD__, $this->getVersion()));
    }

    /**
     * Download report by reference
     *
     * @param string $reference
     * @param string $token
     * @return array
     * @inheritdoc
     * @codeCoverageIgnore deprecated class will be removed
     */
    public function downloadReport($reference, $token)
    {
        throw new \Exception(sprintf("%s not implemented in version %d", __METHOD__, $this->getVersion()));
    }

    /**
     * @param mixed $amount
     * @return string amount formatted to two decimal places with no thousands separator
     */
    public function formatAmount($amount)
    {
        return sprintf("%1\$.2f", $amount);
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return 1;
    }

    /**
     * @param array $fees
     * return string
     */
    protected function getTotalAmountFromFees($fees)
    {
        $totalAmount = 0;
        foreach ($fees as $fee) {
            $totalAmount += (float)$fee->getOutstandingAmount();
        }
        return $this->formatAmount($totalAmount);
    }

    protected function debug($message, $data)
    {
        return $this->logger->debug(
            $message,
            [
                'data' => array_merge(
                    [
                        'domain' => $this->cpmsClient->getOptions()->getDomain(),
                    ],
                    $data
                ),
            ]
        );
    }

    /**
     * Small helper to check if response was successful
     * (We require a successful response code AND a receipt reference)
     *
     * @param array $response response data
     * @return boolean
     */
    protected function isSuccessfulPaymentResponse($response)
    {
        return (
            is_array($response)
            && isset($response['code'])
            && $response['code'] === self::RESPONSE_SUCCESS
            && isset($response['receipt_reference'])
            && !empty($response['receipt_reference'])
        );
    }

    /**
     * Format a date as required by CPMS payment reference fields
     *
     * @param string|DateTime $date
     * @return string
     */
    protected function formatDate($date)
    {
        if (!is_null($date)) {
            if (is_string($date)) {
                $date = new DateTime($date);
            }
            return $date->format(self::DATE_FORMAT);
        }
    }

    /**
     * Gets Customer Reference based on the fees details
     * The method assumes that all fees link to the same organisationId
     *
     * @param array $fees
     * @return int organisationId
     */
    protected function getCustomerReference($fees)
    {
        $reference = 'Miscellaneous'; // default value

        foreach ($fees as $fee) {
            if (!empty($fee->getOrganisation())) {
                $reference = $fee->getOrganisation()->getId();
                break;
            }
        }

        return $reference;
    }
}
