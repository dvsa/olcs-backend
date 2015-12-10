<?php

/**
 * Cpms Helper Interface
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use CpmsClient\Service\ApiService;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Cpms Helper Interface
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
interface CpmsHelperInterface
{
    const PAYMENT_SUCCESS      = 801;
    const PAYMENT_FAILURE      = 802;
    const PAYMENT_CANCELLATION = 807;
    const PAYMENT_IN_PROGRESS  = 800;
    const PAYMENT_GATEWAY_ERROR = 810;
    const PAYMENT_GATEWAY_REDIRECT_URL_RECEIVED = 825;
    const PAYMENT_PAYMENT_CHARGED_BACK = 820;

    const RESPONSE_SUCCESS = '000';

    /**
     * Initiate a card payment
     *
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array $fees
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function initiateCardRequest($redirectUrl, array $fees);

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
    public function initiateStoredCardRequest($redirectUrl, array $fees, $storedCardReference);

    /**
     * Initiate a card not present (CNP) payment
     *
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array $fees
     *
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function initiateCnpRequest($redirectUrl, array $fees);

    /**
     * Update CPMS with payment result
     *
     * @param string $reference payment reference / guid
     * @param array $data response data from the payment gateway
     * @return array|mixed response
     * @see CpmsClient\Service\ApiService::put()
     */
    public function handleResponse($reference, $data);

    /**
     * Determine the status of a payment/transaction
     *
     * @param string $receiptReference
     * @return int status code
     */
    public function getPaymentStatus($receiptReference);

    /**
     * Get the authorisation code for a card payment
     *
     * @param string $receiptReference
     * @return string auth code|null
     */
    public function getPaymentAuthCode($receiptReference);

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
    public function recordCashPayment($fees, $amount, $receiptDate, $slipNo);

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
    public function recordChequePayment($fees, $amount, $receiptDate, $payer, $slipNo, $chequeNo, $chequeDate);

    /**
     * Record a Postal Order payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param array $receiptDate (from DateSelect)
     * @param string $slipNo paying in slip number
     * @param string $poNo Postal Order number
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function recordPostalOrderPayment($fees, $amount, $receiptDate, $slipNo, $poNo);

    /**
     * Get a list of available reports
     *
     * @return array
     */
    public function getReportList();

    /**
     * Get a list of stored debit/credit cards references stored in CPMS
     *
     * @return array
     */
    public function getListStoredCards();

    /**
     * Request report creation
     *
     * @param string $reportCode
     * @param DateTime $start
     * @param DateTime $end
     * @return array
     */
    public function requestReport($reportCode, \DateTime $start, \DateTime $end);

    /**
     * Check report status by reference
     *
     * @param string $reference
     * @return array
     */
    public function getReportStatus($reference);

    /**
     * Download report by reference
     *
     * @param string $reference
     * @param string $token
     * @return array
     */
    public function downloadReport($reference, $token);

    /**
     * Refund payments in a batch
     *
     * @param Fee $fee
     * @return array
     */
    public function batchRefund($fee);

    /**
     * Reverse a cheque, cash, PO or card payment
     *
     * @param string $receiptReference
     * @param string $paymentMethod original payment method, e.g. 'fpm_cash'
     * @param array $fees needed to get customer reference
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function reversePayment($receiptReference, $paymentMethod, $fees = array());

    /**
     * Adjust a transaction
     *
     * @param  Transaction $originalTransaction
     * @param  Transaction $newTransaction
     * @return array CPMS response data
     * @throws CpmsResponseException if response is invalid
     */
    public function adjustTransaction($originalTransaction, $newTransaction);

    /**
     * @param mixed $amount
     * @return string amount formatted to two decimal places with no thousands separator
     */
    public function formatAmount($amount);

    /**
     * @return int
     */
    public function getVersion();
}
