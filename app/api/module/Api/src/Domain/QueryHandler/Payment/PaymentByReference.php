<?php

/**
 * Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Payment;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PaymentByReference extends AbstractQueryHandler
{
    protected $repoServiceName = 'Payment';

    public function handleQuery(QueryInterface $query)
    {
        $payments = $this->getRepo()->fetchbyReference($query->getReference());

        // manually 'serialize' the feePayment and fee children
        // (we have to do this to avoid payment->feePayment->fee->feePayment->payment recursion)
        foreach ($payments['result'] as &$payment) {
            $feePayments = $payment->getFeePayments();
            $fpArray = array();
            foreach ($feePayments as $key => $fp) {
                $fpArray[$key] = $fp->jsonSerialize();
                $fpArray[$key]['fee'] = $fp->getFee()->jsonSerialize();
            }
            $payment = $payment->jsonSerialize();
            $payment['feePayments'] = $fpArray;
        }

        return $payments;
    }
}
