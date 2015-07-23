<?php

/**
 * Fstanding Capital Reserves Bookmark
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Fstanding Capital Reserves Bookmark
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FstandingCapitalReserves extends AbstractQueryHandler
{
    /**
     * @var \Dvsa\Olcs\Api\Service\FinancialStandingHelperService $helper
     */
    protected $helper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->helper = $serviceLocator->getServiceLocator()->get('FinancialStandingHelperService');
        return $this;
    }

    public function handleQuery(QueryInterface $query)
    {
        // for the operator, get vehicle auths for
        // -any new applications where the status is Under consideration or Granted; PLUS
        // -any licences where the status is 'Valid' or 'Suspended' or 'Curtailed'
        $auths = [];

        return $this->helper->getFinanceCalculation($auths);
    }
}
