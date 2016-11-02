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

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Application\PsvVehicles
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->helper = $serviceLocator->getServiceLocator()->get('PsvVehiclesQueryHelper');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /* @var $application Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($query);
        $licenceId = $application->getLicence()->getId();

        /** @var \Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle $lvRepo */
        $lvRepo = $this->getRepo('LicenceVehicle');

        $lvQuery = $lvRepo->createPaginatedVehiclesDataForApplicationQueryPsv(
            $query,
            $application->getId(),
            $licenceId
        );

        $flags = $this->helper->getCommonQueryFlags($application, $query);

        $flags['canTransfer'] = false;
        $flags['hasBreakdown'] = (int) $application->getTotAuthVehicles() > 0;
        $flags['licenceVehicles'] = [
            'results' => $this->resultList(
                $lvRepo->fetchPaginatedList($lvQuery, Query::HYDRATE_OBJECT),
                [
                    'vehicle'
                ]
            ),
            'count' => $lvRepo->fetchPaginatedCount($lvQuery)
        ];
        $flags['activeVehicleCount'] = $application->getActiveVehiclesCount();
        $flags['allVehicleCount'] = $lvRepo->fetchAllVehiclesCount($licenceId);

        return $this->result($application, [], $flags);
    }
}
