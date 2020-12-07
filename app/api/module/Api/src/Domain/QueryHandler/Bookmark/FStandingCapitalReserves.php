<?php

/**
 * FStanding Capital Reserves Bookmark
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * FStanding Capital Reserves Bookmark
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FStandingCapitalReserves extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Organisation'];

    /**
     * @var \Dvsa\Olcs\Api\Service\FinancialStandingHelperService $helper
     */
    protected $helper;

    /**
     * Factory create
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return FStandingCapitalReserves
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->helper = $serviceLocator->getServiceLocator()->get('FinancialStandingHelperService');
        return $this;
    }

    /**
     * For an operator, get the required finance based on:
     *  - any new applications where the status is Under consideration or Granted; PLUS
     *  - any licences where the status is 'Valid' or 'Suspended' or 'Curtailed'
     */
    public function handleQuery(QueryInterface $query)
    {
        return $this->helper->getFinanceCalculationForOrganisation($query->getOrganisation());
    }
}
