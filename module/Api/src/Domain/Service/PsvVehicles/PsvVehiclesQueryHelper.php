<?php

namespace Dvsa\Olcs\Api\Domain\Service\PsvVehicles;

use Dvsa\Olcs\Api\Domain\QueryHandler\ResultList;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Psv Vehicles Query Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehiclesQueryHelper implements FactoryInterface
{
    private $licenceVehicleRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->licenceVehicleRepo = $serviceLocator->get('RepositoryServiceManager')->get('LicenceVehicle');

        return $this;
    }

    public function getCommonQueryFlags($entity, $query)
    {
        $activeCount = $this->getActiveVehiclesCount($entity);

        /*@var */
        return [
            'vehicles' => $this->getVehicles($entity, $query->getIncludeRemoved()),
            'total' => $activeCount,
        ];
    }

    /**
     * Get a list of vehicles
     *
     * @param Application|Licence $entity
     * @param bool $includeRemoved
     *
     * @return ResultList
     */
    private function getVehicles($entity, $includeRemoved = false)
    {
        $licenceVehicles = $this->licenceVehicleRepo->getAllPsvVehicles($entity, $includeRemoved);

        return (new ResultList($licenceVehicles, ['vehicle']))->serialize();
    }

    /**
     * Get count of active vehicles
     *
     * @param Application|Licence $entity
     *
     * @return int
     */
    private function getActiveVehiclesCount($entity)
    {
        return $this->licenceVehicleRepo->getAllPsvVehicles($entity)->count();
    }
}
