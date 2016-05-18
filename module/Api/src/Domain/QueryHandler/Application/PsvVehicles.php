<?php

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Service\PsvVehicles\PsvVehiclesQueryHelper;
use Doctrine\ORM\Query;

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehicles extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';
    protected $extraRepos = ['LicenceVehicle'];

    /**
     * @var PsvVehiclesQueryHelper
     */
    protected $helper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->helper = $serviceLocator->getServiceLocator()->get('PsvVehiclesQueryHelper');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $application Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($query);

        $lvQuery = $this->getRepo('LicenceVehicle')->createPaginatedVehiclesDataForApplicationQueryPsv(
            $query,
            $application->getId(),
            $application->getLicence()->getId()
        );

        $flags = $this->helper->getCommonQueryFlags($application, $query);

        $flags['canTransfer'] = false;
        $flags['hasBreakdown'] = (int) $application->getTotAuthVehicles() > 0;
        $flags['licenceVehicles'] = [
            'results' => $this->resultList(
                $this->getRepo('LicenceVehicle')->fetchPaginatedList($lvQuery, Query::HYDRATE_OBJECT),
                [
                    'vehicle'
                ]
            ),
            'count' => $this->getRepo('LicenceVehicle')->fetchPaginatedCount($lvQuery)
        ];
        $flags['allVehicleCount'] = $application->getAllVehiclesCount();

        return $this->result($application, [], $flags);
    }
}
