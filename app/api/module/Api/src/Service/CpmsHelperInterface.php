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
    const PAYMENT_SUCCESS      = 801;
    const PAYMENT_FAILURE      = 802;
    const PAYMENT_CANCELLATION = 807;
    const PAYMENT_IN_PROGRESS  = 800;

    const RESPONSE_SUCCESS = '000';

    // CPMS' preferred date format (note: this changed around 03/2015)
    const DATE_FORMAT = 'Y-m-d';

    // @TODO product ref shouldn't have to come from a whitelist...
    const PRODUCT_REFERENCE = 'GVR_APPLICATION_FEE';

    // @TODO this is a dummy value for testing purposes as cost_centre is now
    // a required parameter in cpms/payment-service. Awaiting further info on
    // what OLCS should pass for this field.
    const COST_CENTRE = '12345,67890';

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
