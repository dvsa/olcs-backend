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
        $payment = $this->getRepo()->fetchByReference($query->getReference());

        return $this->result(
            $payment,
            [
                'feePayments' => [
                    'fee' => [
                        'application',
                        'licence' => [
                            'organisation',
                        ],
                    ],
                ]
            ]
        );
    }
}
