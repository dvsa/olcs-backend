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
    protected $repoServiceName = 'Application';

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

    /**
     * For an operator, get the required finance based on:
     *  - any new applications where the status is Under consideration or Granted; PLUS
     *  - any licences where the status is 'Valid' or 'Suspended' or 'Curtailed'
     */
    public function handleQuery(QueryInterface $query)
    {
        $auths = [];
        $organisation = $query->getOrganisation();

        $applications = $this->getRepo()->fetchActiveForOrganisation($organisation->getId());
        if (!empty($applications)) {
            foreach ($applications as $app) {
                // filter new apps only
                if ($app->isVariation()) {
                    continue;
                }
                $auths[] = [
                    'type' => $app->getTypeOfLicence()->getId(),
                    'count' => $app->getTotAuthVehicles(),
                    'category' => $app->getGoodsOrPsv()->getId(),
                ];
            }
        }

        $licences = $organisation->getActiveLicences();
        if (!empty($licences)) {
            foreach ($licences as $licence) {
                $auths[] = [
                    'type' => $licence->getTypeOfLicence()->getId(),
                    'count' => $licence->getTotAuthVehicles(),
                    'category' => $licence->getGoodsOrPsv()->getId(),
                ];
            }
        }

        return $this->helper->getFinanceCalculation($auths);
    }
}
