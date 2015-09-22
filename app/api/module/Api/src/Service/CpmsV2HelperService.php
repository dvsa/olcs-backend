<?php

/**
 * Cpms Version 2 Helper Service
 *
 * Note: CPMS has been known to reject ints as 'missing', so we cast
 * some fields (ID's, etc.) to strings
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use CpmsClient\Service\ApiService;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;

/**
 * Cpms Version 2 Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsV2HelperService implements FactoryInterface, CpmsHelperInterface
{
    // CPMS' preferred date format (note: this changed around 03/2015)
    const DATE_FORMAT = 'Y-m-d';

    const PRODUCT_REFERENCE = 'GVR_APPLICATION_FEE';

    // @TODO this is a dummy value for testing purposes as cost_centre is now
    // a required parameter in cpms/payment-service. Awaiting further info on
    // what OLCS should pass for this field.
    const COST_CENTRE = '12345,67890';

    const TAX_CODE = 'Z';

    /**
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var ApiService
     */
    protected $cpmsClient;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return self
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->cpmsClient = $serviceLocator->get('cpms\service\api');
        $this->logger = $serviceLocator->get('Logger');
        $this->feesHelper = $serviceLocator->get('FeesHelperService');
        return $this;
    }

    /**
     * @return ApiService
     */
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
    public function initiateCardRequest($redirectUrl, array $fees)
    {
        $method   = 'post';
        $endPoint = '/api/payment/card';
        $scope    = ApiService::SCOPE_CARD;

        $extraParams = [
            'redirect_uri' => $redirectUrl,
            'disable_redirection' => true, // legacy??
            'scope' => $scope,
        ];
        $params = $this->getParametersForFees($fees, $extraParams);

        foreach ($fees as $fee) {
            $params['payment_data'][] = $this->getPaymentDataForFee($fee);
        }

        $response = $this->send($method, $endPoint, $scope, $params);

        return $this->validatePaymentResponse($response, false);
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
     * @return int status code|null
     */
    public function getPaymentStatus($receiptReference)
    {
        $method   = 'get';
        $endPoint = '/api/payment/'.$receiptReference;
        $scope    = ApiService::SCOPE_QUERY_TXN;

        $params = [
            'required_fields' => [
                'payment' => [
                    'payment_status'
                ]
            ]
        ];

        $response = $this->send($method, $endPoint, $scope, $params);

        if (isset($response['payment_status']['code'])) {
            return $response['payment_status']['code'];
        }
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
     *
     * @todo $payer appears to be no longer required, but retained to keep the
     * interface the same as v1
     */
    public function recordCashPayment($fees, $amount, $receiptDate, $payer, $slipNo)
    {
        unset($payer); // unused

        $method   = 'post';
        $endPoint = '/api/payment/cash';
        $scope    = ApiService::SCOPE_CASH;

        $extraParams = [
            'slip_number' => (string) $slipNo,
            'batch_number' => (string) $slipNo,
            'receipt_date' => $this->formatDate($receiptDate),
            'scope' => $scope,
            'total_amount' => $this->formatAmount($amount),
        ];
        $params = $this->getParametersForFees($fees, $extraParams);

        $allocations = $this->feesHelper->allocatePayments($amount, $fees);

        foreach ($fees as $fee) {
            $extraPaymentData = ['allocated_amount' => $allocations[$fee->getId()]];
            $paymentData = $this->getPaymentDataForFee($fee, $extraPaymentData);
            if (!empty($paymentData)) {
                $params['payment_data'][] = $paymentData;
            }
        }

        $response = $this->send($method, $endPoint, $scope, $params);

        return $this->validatePaymentResponse($response);
    }

    /**
     * Record a cheque payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param string $receiptDate (from DateSelect)
     * @param string $payer payer name
     * @param string $slipNo paying in slip number
     * @param string $chequeNo cheque number
     * @param string $chequeDate (from DateSelect)
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function recordChequePayment($fees, $amount, $receiptDate, $payer, $slipNo, $chequeNo, $chequeDate)
    {
        $method   = 'post';
        $endPoint = '/api/payment/cheque';
        $scope    = ApiService::SCOPE_CHEQUE;

        $extraParams = [
            'cheque_date' => $this->formatDate($chequeDate),
            'cheque_number' => (string) $chequeNo,
            'slip_number' => (string) $slipNo,
            'batch_number' => (string) $slipNo,
            'receipt_date' => $this->formatDate($receiptDate),
            'name_on_cheque' => $payer,
            'scope' => $scope,
            'total_amount' => $this->formatAmount($amount),
        ];
        $params = $this->getParametersForFees($fees, $extraParams);

        $allocations = $this->feesHelper->allocatePayments($amount, $fees);

        foreach ($fees as $fee) {
            $extraPaymentData = ['allocated_amount' => $allocations[$fee->getId()]];
            $paymentData = $this->getPaymentDataForFee($fee, $extraPaymentData);
            if (!empty($paymentData)) {
                $params['payment_data'][] = $paymentData;
            }
        }

        $response = $this->send($method, $endPoint, $scope, $params);

        return $this->validatePaymentResponse($response);
    }

    /**
     * Record a Postal Order payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param string $receiptDate (from DateSelect)
     * @param string $payer payer name
     * @param string $slipNo paying in slip number
     * @param string $poNo Postal Order number
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     *
     * @todo $payer appears to be no longer required, but retained to keep the
     * interface the same as v1
     */
    public function recordPostalOrderPayment($fees, $amount, $receiptDate, $payer, $slipNo, $poNo)
    {
        unset($payer); // unused

        $method   = 'post';
        $endPoint = '/api/payment/postal-order';
        $scope    = ApiService::SCOPE_POSTAL_ORDER;

        $extraParams = [
            'postal_order_number' => (string) $poNo,
            'slip_number' => (string) $slipNo,
            'batch_number' => (string) $slipNo,
            'receipt_date' => $this->formatDate($receiptDate),
            'scope' => $scope,
            'total_amount' => $this->formatAmount($amount),
        ];
        $params = $this->getParametersForFees($fees, $extraParams);

        $allocations = $this->feesHelper->allocatePayments($amount, $fees);

        foreach ($fees as $fee) {
            $extraPaymentData = ['allocated_amount' => $allocations[$fee->getId()]];
            $paymentData = $this->getPaymentDataForFee($fee, $extraPaymentData);
            if (!empty($paymentData)) {
                $params['payment_data'][] = $paymentData;
            }
        }

        $response = $this->send($method, $endPoint, $scope, $params);

        return $this->validatePaymentResponse($response);
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
        return 2;
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
     * @param Dvsa\Olcs\Api\Entity\ContactDetails\Address $address
     * @return array
     */
    protected function formatAddress($address)
    {
         return [
            'line_1' => $address->getAddressLine1(),
            'line_2' => $address->getAddressLine2(),
            'line_3' => $address->getAddressLine3(),
            'line_4' => $address->getAddressLine4(),
            'city' => $address->getTown(),
            'postcode' => $address->getPostcode(),
        ];
    }

    /**
     * @param array $fees
     * return string
     */
    protected function getTotalAmountFromFees($fees)
    {
        $totalAmount = 0;
        foreach ($fees as $fee) {
            $totalAmount += (int) ($fee->getOutstandingAmount() * 100);
        }
        return $this->formatAmount($totalAmount / 100);
    }

    /**
     * Small helper to check if response was successful
     * (We require a successful response code AND a receipt reference)
     * Returns the response if OK, otherwise throws an exception
     *
     * @param array $response response data
     * @param boolean $requireSuccessCode
     * @return array
     * @throws CpmsResponseException
     */
    protected function validatePaymentResponse($response, $requireSuccessCode = true)
    {

        // check it's an array
        if (is_array($response)) {

            // check we have receipt reference
            if (isset($response['receipt_reference']) && !empty($response['receipt_reference'])) {

                // check we have a success code if required
                if (!$requireSuccessCode) {
                    return $response;
                }
                if (isset($response['code']) && $response['code'] === self::RESPONSE_SUCCESS) {
                    return $response;
                }
            }
        }

        $e = new CpmsResponseException('Invalid payment response');
        $e->setResponse($response);
        throw $e;
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

    /**
     * Get data for 'payment_data' elements of a payment request
     *
     * @param Fee $fee
     * @param array $extraPayment data
     * @return array|null (will return null if we don't want to include a fee,
     * e.g. overpayment balancing fees)
     *
     * @todo 'product_reference' should be $fee->getFeeType()->getDescription()
     * but CPMS has a whitelist and responds  {"code":104,"message":"product_reference is invalid"}
     * @todo 'sales_person_reference'
     */
    protected function getPaymentDataForFee(Fee $fee, $extraPaymentData = [])
    {
        if ($fee->isBalancingFee()) {
            return;
        }

        $commonPaymentData = [
            'line_identifier' => (string) $fee->getInvoiceLineNo(),
            'amount' => $this->formatAmount($fee->getAmount()),
            'allocated_amount' => $this->formatAmount(
                // may be overridden if under/overpayment
                $fee->getOutstandingAmount()
            ),
            // all fees are currently zero rated
            'net_amount' => $this->formatAmount($fee->getAmount()),
            'tax_amount' => '0.00',
            'tax_code' => self::TAX_CODE,
            'tax_rate' => '0',
            'invoice_date' => $this->formatDate($fee->getInvoicedDate()),
            'sales_reference' => (string) $fee->getId(),
            'product_reference' => self::PRODUCT_REFERENCE,
            'product_description' => $fee->getFeeType()->getDescription(),
            'receiver_reference' => (string) $this->getCustomerReference([$fee]),
            'receiver_name' => $fee->getCustomerNameForInvoice(),
            'receiver_address' => $this->formatAddress($fee->getCustomerAddressForInvoice()),
            'rule_start_date' => $this->formatDate($fee->getRuleStartDate()),
            'deferment_period' => (string) $fee->getDefermentPeriod(),
            // 'country_code' ('GB' or 'NI') is optional and deliberately omitted
            'sales_person_reference' => '',
        ];

        return array_merge($commonPaymentData, $extraPaymentData);
    }

    /**
     * Get top-level data for a payment request
     *
     * @param array $fees array of Fee objects
     * @return array
     */
    protected function getParametersForFees(array $fees, array $extraParams)
    {
        if (empty($fees)) {
            return [];
        }

        $totalAmount = $this->getTotalAmountFromFees($fees);
        $firstFee = reset($fees);
        $commonParams = [
            'customer_reference' => (string) $this->getCustomerReference($fees),
            'payment_data' => [],
            'cost_centre' => self::COST_CENTRE,
            'total_amount' => $this->formatAmount($totalAmount),
            'customer_name' => $firstFee->getCustomerNameForInvoice(),
            'customer_manager_name' => $firstFee->getCustomerNameForInvoice(),
            'customer_address' => $this->formatAddress($firstFee->getCustomerAddressForInvoice()),
            'refund_overpayment' => $this->isOverpayment($fees),
        ];

        return array_merge($commonParams, $extraParams);
    }

    /**
     * Determine if an array of fees contains an overpayment
     *
     * @return boolean
     */
    protected function isOverpayment($fees)
    {
        foreach ($fees as $fee) {
            if ($fee->isBalancingFee()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send a request via the CPMS client and log request/response
     *
     * @param string $endPoint
     * @param string $scope
     * @param array $params
     * @return array|mixed cpms client response
     */
    protected function send($method, $endPoint, $scope, $params)
    {
        $this->debug(
            "CPMS $scope request",
            [
                'endPoint' => $endPoint,
                'scope'    => $scope,
                'params'   => $params,
            ]
        );

        $response = $this->getClient()->$method($endPoint, $scope, $params);

        $this->debug("CPMS $scope response", ['response' => $response]);

        return $response;
    }

    protected function debug($message, $data)
    {
        return $this->logger->debug(
            $message,
            [
                'data' => array_merge(
                    [
                        'version' => $this->getVersion(),
                        'domain' => $this->getClient()->getOptions()->getDomain(),
                    ],
                    $data
                ),
            ]
        );
    }
}
