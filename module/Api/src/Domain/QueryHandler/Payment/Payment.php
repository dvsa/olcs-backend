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
class Payment extends AbstractQueryHandler
{
    protected $repoServiceName = 'Payment';


    public function handleQuery(QueryInterface $query)
    {
        // @TODO include fee payment/fee details - fee repo?
        return $this->getRepo()->fetchbyReference($query->getReference());
    }
}
