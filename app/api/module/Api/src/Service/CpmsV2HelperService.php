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
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cpms Version 2 Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsV2HelperService implements FactoryInterface, CpmsHelperInterface
{
    // CPMS' preferred date format (note: this changed around 03/2015)
    const DATE_FORMAT = 'Y-m-d';

    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    const REFUND_REASON = 'Refund';

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
     *
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function initiateCardRequest($redirectUrl, array $fees)
    {
        $endPoint = '/api/payment/card';
        $scope    = ApiService::SCOPE_CARD;

        return $this->initiateRequest($redirectUrl, $fees, $endPoint, $scope);
    }

    /**
     * Initiate a stored card payment payment
     *
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array  $fees
     * @param string $storedCardReference Stored card reference
     *
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function initiateStoredCardRequest($redirectUrl, array $fees, $storedCardReference)
    {
        $endPoint = '/api/payment/stored-card/'. $storedCardReference;
        $scope    = ApiService::SCOPE_STORED_CARD;

        return $this->initiateRequest($redirectUrl, $fees, $endPoint, $scope);
    }

    /**
     * Initiate a card not present (CNP) payment
     *
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array $fees
     *
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function initiateCnpRequest($redirectUrl, array $fees)
    {
        $endPoint = '/api/payment/cardholder-not-present';
        $scope    = ApiService::SCOPE_CNP;

        return $this->initiateRequest($redirectUrl, $fees, $endPoint, $scope);
    }

    /**
     * Initiate a payment request
     *
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array $fees
     * @param string $endPoint Either card or CNP endpoint
     * @param string $scope    Either ApiService::SCOPE_CNP or ApiService::SCOPE_CARD
     *
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    private function initiateRequest($redirectUrl, array $fees, $endPoint, $scope)
    {
        $method   = 'post';
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
     * Get the authorisation code for a card payment
     *
     * Note: this is potentially required for chargebacks, etc. but is not
     * currently used
     *
     * @param string $receiptReference
     * @return string auth code|null
     */
    public function getPaymentAuthCode($receiptReference)
    {
        $method   = 'get';
        $endPoint = '/api/payment/'.$receiptReference.'/auth-code';
        $scope    = ApiService::SCOPE_QUERY_TXN;

        $response = $this->send($method, $endPoint, $scope, []);

        if (isset($response['auth_code'])) {
            return $response['auth_code'];
        }
    }

    /**
     * Record a cash payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param string|DateTime $receiptDate
     * @param string $slipNo paying in slip number
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function recordCashPayment($fees, $amount, $receiptDate, $slipNo)
    {
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
     * @param string $slipNo paying in slip number
     * @param string $poNo Postal Order number
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function recordPostalOrderPayment($fees, $amount, $receiptDate, $slipNo, $poNo)
    {
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
     * Get a list of available reports
     *
     * @return array
     */
    public function getReportList()
    {
        return $this->send('get', '/api/report', ApiService::SCOPE_REPORT, []);
    }

    /**
     * Get a list of stored debit/credit cards references stored in CPMS
     *
     * @return array
     */
    public function getListStoredCards()
    {
        return $this->send('get', '/api/stored-card', ApiService::SCOPE_STORED_CARD, []);
    }

    /**
     * Request report creation
     *
     * @param string $reportCode
     * @param DateTime $start
     * @param DateTime $end
     * @return array
     */
    public function requestReport($reportCode, \DateTime $start, \DateTime $end)
    {
        $params = [
            'report_code' => (string) $reportCode,
            'filters' => [
                'from' => $this->formatDateTime($start),
                'to' => $this->formatDateTime($end),
            ],
        ];

        return $this->send('post', '/api/report', ApiService::SCOPE_REPORT, $params);
    }

    /**
     * Check report status by reference
     *
     * @param string $reference
     * @return array
     */
    public function getReportStatus($reference)
    {
        $endPoint = '/api/report/'.$reference.'/status';

        return $this->send('get', $endPoint, ApiService::SCOPE_REPORT, []);
    }

    /**
     * Download report by reference
     *
     * @param string $reference
     * @param string $token
     * @return array
     */
    public function downloadReport($reference, $token)
    {
        $url = '/api/report/'.$reference.'/download?token='.$token;

        return $this->send('get', $url, ApiService::SCOPE_REPORT, []);
    }

    /**
     * Refund payments in a batch
     *
     * @param Fee $fee
     * @return array
     * @throws CpmsResponseException if response is invalid
     */
    public function batchRefund($fee)
    {
        $method   = 'post';
        $endPoint = '/api/refund';
        $scope    = ApiService::SCOPE_REFUND;

        $payments = [];

        foreach ($fee->getFeeTransactionsForRefund() as $ft) {
            $payments[] = $this->getRefundPaymentDataForFeeTransaction($ft);
        }

        $params = [
            'scope' => $scope,
            'customer_reference' => (string) $this->getCustomerReference([$fee]),
            'payments' => $payments,
        ];

        $response = $this->send($method, $endPoint, $scope, $params);

        if (isset($response['code']) && $response['code'] === self::RESPONSE_SUCCESS) {
            return $response;
        } else {
            $e = new CpmsResponseException('Invalid refund response');
            $e->setResponse($response);
            throw $e;
        }
    }

    /**
     * @todo remove when CPMS refund end point is working
     */
    private function stubBatchRefundResponse($payments)
    {
        $receiptRefs = array_map(
            function ($payment) {
                return $payment['receipt_reference'];
            },
            $payments
        );
        $count = 0;
        $refundRefs = array_map(
            function ($payment) use (&$count) {
                return sprintf('REFUND-REF-%d', ++$count);
            },
            $receiptRefs
        );
        return [
           'code' => self::RESPONSE_SUCCESS,
           'receipt_references' => array_combine($receiptRefs, $refundRefs),
           'message' => '** stubbed response from ' . __METHOD__ . ' **',
        ];
    }

    /**
     * @param FeeTransaction $ft
     * @return array of 'payment' data for batch refund call
     * @see https://wiki.i-env.net/display/CPMS/CPMS+API+V2+Specification#CPMSAPIV2Specification-Batchrefund
     */
    protected function getRefundPaymentDataForFeeTransaction(FeeTransaction $ft)
    {
        $paymentData = $this->getPaymentDataForFee(
            $ft->getFee(),
            [
                'allocated_amount' => $this->formatAmount($ft->getAmount()),
            ]
        );

        return [
            'receipt_reference' => $ft->getTransaction()->getReference(),
            'refund_reason' => self::REFUND_REASON,
            'payment_data' => [
                $paymentData,
            ]
        ];
    }

    /**
     * Reverse a cheque, cash, PO or card payment
     *
     * @param string $receiptReference
     * @param string $paymentMethod original payment method, e.g. 'fpm_cash'
     * @param array $fees needed to get customer reference
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function reversePayment($receiptReference, $paymentMethod, $fees = array())
    {
        $method   = 'post';
        $endPoint = '/api/payment/'.$receiptReference.'/reversal';

        $scopeMap = [
            Fee::METHOD_CHEQUE       => ApiService::CHEQUE_RD,
            Fee::METHOD_CARD_ONLINE  => ApiService::SCOPE_CHARGE_BACK,
            Fee::METHOD_CARD_OFFLINE => ApiService::SCOPE_CHARGE_BACK,
            Fee::METHOD_CASH         => ApiService::SCOPE_CASH,
            Fee::METHOD_POSTAL_ORDER => ApiService::SCOPE_POSTAL_ORDER,
        ];

        $scope = $scopeMap[$paymentMethod];

        if (in_array($paymentMethod, [Fee::METHOD_CARD_ONLINE, Fee::METHOD_CARD_OFFLINE])) {
            // for card reversals, switch endpoint to 'charge back'
            $endPoint = '/api/payment/'.$receiptReference.'/chargeback';
        }

        $params = [
            'scope' => $scope,
            'customer_reference' => (string) $this->getCustomerReference($fees),
        ];

        $response = $this->send($method, $endPoint, $scope, $params);

        return $this->validatePaymentResponse($response, false);
    }

    /**
     * Adjust a transaction
     *
     * @param  Transaction $originalTransaction
     * @param  Transaction $newTransaction
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function adjustTransaction($originalTransaction, $newTransaction)
    {
        $method   = 'post';
        $endPoint = '/api/payment/'.$originalTransaction->getReference().'/adjustment';
        $scope    = ApiService::SCOPE_ADJUSTMENT;

        $newAmount = $newTransaction->getAmountAfterAdjustment();
        $fees = $newTransaction->getFees();

        $chequeNo = $poNo = null;
        switch ($newTransaction->getPaymentMethod()->getId()) {
            case Fee::METHOD_CHEQUE:
                $chequeNo = $newTransaction->getChequePoNumber();
                break;
            case Fee::METHOD_POSTAL_ORDER:
                $poNo = $newTransaction->getChequePoNumber();
                break;
            default:
                break;
        }

        $extraParams = [
            'cheque_date' => $this->formatDate($newTransaction->getChequePoDate()),
            'cheque_number' => (string) $chequeNo,
            'postal_order_number' => (string) $poNo,
            'slip_number' => (string) $newTransaction->getPayingInSlipNumber(),
            'batch_number' => (string) $newTransaction->getPayingInSlipNumber(),
            'name_on_cheque' => $newTransaction->getPayerName(),
            'scope' => $scope,
            'total_amount' => $this->formatAmount($newAmount),
        ];
        $params = $this->getParametersForFees($fees, $extraParams);

        foreach ($fees as $fee) {
            if ($fee->isBalancingFee()) {
                continue;
            }
            $allocation = $newTransaction->getAmountAllocatedToFeeId($fee->getId());
            $extraPaymentData = ['allocated_amount' => $allocation];
            $paymentData = $this->getPaymentDataForFee($fee, $extraPaymentData);
            if (!empty($paymentData)) {
                $params['payment_data'][] = $paymentData;
            }
        }

        $response = $this->send($method, $endPoint, $scope, $params);

        return $this->validatePaymentResponse($response, false);
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
     * Format a date/time as required by CPMS report filter fields
     *
     * @param DateTime $dateTime
     * @return string
     */
    protected function formatDateTime(\DateTime $dateTime)
    {
        return $dateTime->format(self::DATETIME_FORMAT);
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
            $totalAmount += Fee::amountToPence($fee->getOutstandingAmount());
        }
        return $this->formatAmount(Fee::amountToPounds($totalAmount));
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
     */
    protected function getPaymentDataForFee(Fee $fee, $extraPaymentData = [])
    {
        if ($fee->isBalancingFee()) {
            return;
        }

        $commonPaymentData = [
            'line_identifier' => (string) $fee->getId(),
            'amount' => $this->formatAmount($fee->getGrossAmount()),
            'allocated_amount' => $this->formatAmount(
                // may be overridden if under/overpayment
                $fee->getOutstandingAmount()
            ),
            // all fees are currently zero rated
            'net_amount' => $this->formatAmount($fee->getNetAmount()),
            'tax_amount' => $this->formatAmount($fee->getVatAmount()),
            'tax_code' => $fee->getFeeType()->getVatCode(),
            'tax_rate' => $fee->getFeeType()->getVatRate(),
            'invoice_date' => $this->formatDate($fee->getInvoicedDate()),
            'sales_reference' => (string) $fee->getId(),
            // note, as per OLCS-11438 product_reference should come from the
            // fee_type description, NOT the product_reference column!
            'product_reference' => $fee->getFeeType()->getDescription(),
            'product_description' => $fee->getFeeType()->getDescription(),
            'receiver_reference' => (string) $this->getCustomerReference([$fee]),
            'receiver_name' => $fee->getCustomerNameForInvoice(),
            'receiver_address' => $this->formatAddress($fee->getCustomerAddressForInvoice()),
            'rule_start_date' => $this->formatDate($fee->getRuleStartDate()),
            'deferment_period' => (string) $fee->getDefermentPeriod(),
            'country_code' => $fee->getFeeType()->getCountryCode(),
            'sales_person_reference' => $fee->getSalesPersonReference(),
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
        $method = strtolower($method);

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
        return Logger::debug(
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
