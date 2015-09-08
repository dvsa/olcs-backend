<?php

/**
 * Cpms Helper Interface
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use CpmsClient\Service\ApiService;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cpms Helper Interface
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
interface CpmsHelperInterface
{
    /**
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array $fees
     * @param string $paymentMethod 'fpm_card_offline'|'fpm_card_online'
     *
     * @return array
     */
    public function initiateCardRequest($redirectUrl, array $fees);

    /**
     * Update CPMS with payment result
     * @param string $reference payment reference / guid
     * @param array $data response data from the payment gateway
     */
    public function handleResponse($reference, $data);

    /**
     * Determine the status of a payment
     *
     * @param string $receiptReference
     * @return int status
     */
    public function getPaymentStatus($receiptReference);

    /**
     * Record a cash payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param string|DateTime $receiptDate
     * @param string $payer payer name
     * @param string $slipNo paying in slip number
     * @return array|false only return successful response, otherwise false
     */
    public function recordCashPayment($fees, $amount, $receiptDate, $payer, $slipNo);

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
     * @return array|false only return successful response, otherwise false
     */
    public function recordChequePayment($fees, $amount, $receiptDate, $payer, $slipNo, $chequeNo, $chequeDate);

    /**
     * Record a Postal Order payment in CPMS
     *
     * @param array $fees
     * @param float $amount
     * @param array $receiptDate (from DateSelect)
     * @param string $payer payer name
     * @param string $slipNo paying in slip number
     * @param string $poNo Postal Order number
     * @return array|false only return successful response, otherwise false
     */
    public function recordPostalOrderPayment($fees, $amount, $receiptDate, $payer, $slipNo, $poNo);

    /**
     * @param mixed $amount
     * @return string amount formatted to two decimal places with no thousands separator
     */
    public function formatAmount($amount);
}
