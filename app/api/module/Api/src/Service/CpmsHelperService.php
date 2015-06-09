<?php

/**
 * Cpms Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CpmsClient\Service\ApiService;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;

/**
 * Cpms Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsHelperService implements FactoryInterface
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
     * @param string $customerReference usually organisation id
     * @param string $redirectUrl redirect back to here from payment gateway
     * @param array $fees
     * @param string $paymentMethod 'fpm_card_offline'|'fpm_card_online'
     *
     * @return array
     * @throws Common\Service\Cpms\Exception\PaymentInvalidResponseException on error
     */
    public function initiateCardRequest(
        $customerReference,
        $redirectUrl,
        array $fees,
        $paymentMethod
    ) {
        $totalAmount = $this->getTotalAmountFromFees($fees);

        $paymentData = [];
        foreach ($fees as $fee) {
            $paymentData[] = [
                'amount' => $this->formatAmount($fee->getAmount()),
                'sales_reference' => (string)$fee->getId(),
                'product_reference' => self::PRODUCT_REFERENCE,
                'payment_reference' => [
                    'rule_start_date' => $fee->getRuleStartDate()->format(self::DATE_FORMAT),
                ],
            ];
        }

        $endPoint = '/api/payment/card';
        $scope    = ApiService::SCOPE_CARD;

        $params = [
            // @NOTE CPMS rejects ints as 'missing', so we have to force a string...
            'customer_reference' => (string)$customerReference,
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
            throw new \Exception('Invalid payment response: '.json_encode($response));
        }

        return $response;
    }

    // @TODO make complete/resovle payment command
    /**
     * @param array $data response data from the payment gateway
     * @param string $paymentMethod 'fpm_card_offline'|'fpm_card_online'
     * @throws Common\Service\Cpms\Exception\PaymentNotFoundException
     * @throws Common\Service\Cpms\Exception\PaymentInvalidStatusException
     * @throws Common\Service\Cpms\Exception
     */
    public function handleResponse($data, $paymentMethod)
    {
        if (!isset($data['receipt_reference'])) {
            throw new \Exception('No receipt_reference received from CPMS gateway');
        }

        $reference = $data['receipt_reference'];

        /**
         * 1) Check what status we think this payment is currently in
         */
        $payment = $paymentService->getDetails($reference);
        // @TODO
        $query = PaymentByReference::create(['reference' => $reference);
        $foo->send>$query;

        if ($payment === false) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Payment not found');
        }

        if ($payment->getStatus()->getId() !== PaymentEntity::STATUS_OUTSTANDING) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\RuntimeException(
                'Invalid payment status: ' . $payment->getStatus()->getId()
            );
        }

        /**
         * 2) Let CPMS know the response from the payment gateway
         *
         * We have to bundle up the response data verbatim as it can
         * vary per gateway implementation
         */
        $this->getClient()->put('/api/gateway/' . $reference . '/complete', ApiService::SCOPE_CARD, $data);

        /**
         * 3) Now actually look up the status of the transaction and
         * update our payment record & fee(s) accordingly
         */
        return $this->resolvePayment($reference, $payment->getId(), $paymentMethod);
    }

    /**
     * @param string $reference receipt reference/guid
     * @param int $paymentId OLCS payment id
     * @param string $paymentMethod 'fpm_card_offline'|'fpm_card_online'
     * @return int payment status
     */
    public function resolvePayment($reference, $paymentId, $paymentMethod)
    {
        throw new \Exception(__METHOD__.' @TODO');
        $paymentService = $this->getServiceLocator()->get('Entity\Payment');
        $paymentStatus  = $this->getPaymentStatus($reference);

        $now = $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s');

        if ($paymentStatus == self::PAYMENT_SUCCESS) {
            $fees = $this->getServiceLocator()->get('Entity\FeePayment')
                ->getFeesByPaymentId($paymentId);
            foreach ($fees as $fee) {
                $data = [
                    'feeStatus'      => FeeEntityService::STATUS_PAID,
                    'receivedDate'   => $now,
                    'receiptNo'      => $reference,
                    'paymentMethod'  => $paymentMethod,
                    'receivedAmount' => $fee['amount']
                ];

                $this->updateFeeRecordAsPaid($fee['id'], $data);
            }
            $status = PaymentEntity::STATUS_PAID;
            $paymentService->forceUpdate(
                $paymentId,
                [
                    'status' => $status,
                    'completedDate' => $now,
                ]
            );
            return $status;
        }

        // handle non-paid statuses
        switch ($paymentStatus) {
            case self::PAYMENT_FAILURE:
                $status = PaymentEntity::STATUS_FAILED;
                break;
            case self::PAYMENT_CANCELLATION:
                $status = PaymentEntity::STATUS_CANCELLED;
                break;
            case self::PAYMENT_IN_PROGRESS:
                // resolve any abandoned payments as 'failed'
                $status = PaymentEntity::STATUS_FAILED;
                break;
            default:
                $this->log('Unknown CPMS payment_status: ' . $paymentStatus);
                $status = null;
                break;
        }

        if ($status !== null) {
            $paymentService->setStatus($paymentId, $status);
            return $status;
        }
    }

    /**
     * Determine the status of a payment
     *
     * @param string $receiptReference
     * @return int status
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

    protected function resolveOutstandingPayments($fee)
    {
        // @TODO!
        return false;
    }

    /**
     * @param array $fees
     * return float
     */
    protected function getTotalAmountFromFees($fees)
    {
        $totalAmount = 0;
        foreach ($fees as $fee) {
            $totalAmount += (float)$fee->getAmount();
        }
        return $totalAmount;
    }

    /**
     * @param mixed $amount
     * @return string amount formatted to two decimal places with no thousands separator
     */
    protected function formatAmount($amount)
    {
        if (!empty($amount) && !is_numeric($amount)) {
            throw new \InvalidArgumentException("'".var_export($amount, true)."' is not a valid amount");
        }
        return sprintf("%1\$.2f", $amount);
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

    public function resolvePaidFees($fees)
    {
        $feesToPay = [];
        foreach ($fees as $fee) {
            if ($fee->hasOutstandingPayment()) {
                $paid = $this->resolveOutstandingPayments($fee);
                if (!$paid) {
                    $feesToPay[] = $fee;
                }
            } else {
                $feesToPay[] = $fee;
            }
        }
        return $feesToPay;
    }
}
