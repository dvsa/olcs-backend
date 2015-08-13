<?php

namespace Dvsa\Olcs\Api\Domain\Service\PsvVehicles;

use Dvsa\Olcs\Api\Domain\QueryHandler\ResultList;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity;

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
        $small = $this->getSmallVehicles($entity, $query->getIncludeRemoved());
        $medium = $this->getMediumVehicles($entity, $query->getIncludeRemoved());
        $large = $this->getLargeVehicles($entity, $query->getIncludeRemoved());

        $smallCount = count($small);
        $mediumCount = count($medium);
        $largeCount = count($large);

        $activeSmallCount = $this->countActiveSmallVehicles($entity);
        $activeMediumCount = $this->countActiveMediumVehicles($entity);
        $activeLargeCount = $this->countActiveLargeVehicles($entity);

        return [
            'showSmallTable' => $entity->shouldShowSmallTable($smallCount),
            'showMediumTable' => $entity->shouldShowMediumTable($mediumCount),
            'showLargeTable' => $entity->shouldShowLargeTable($largeCount),
            'smallAuthExceeded' => $entity->isSmallAuthExceeded($activeSmallCount),
            'mediumAuthExceeded' => $entity->isMediumAuthExceeded($activeMediumCount),
            'largeAuthExceeded' => $entity->isLargeAuthExceeded($activeLargeCount),
            'availableSmallSpaces' => $entity->getAvailableSmallSpaces($activeSmallCount),
            'availableMediumSpaces' => $entity->getAvailableMediumSpaces($activeMediumCount),
            'availableLargeSpaces' => $entity->getAvailableLargeSpaces($activeLargeCount),
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'total' => $activeSmallCount + $activeMediumCount + $activeLargeCount,
        ];
    }

    private function countActiveSmallVehicles($entity)
    {
        return $this->getActiveVehiclesCount($entity, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL);
    }

    private function countActiveMediumVehicles($entity)
    {
        return $this->getActiveVehiclesCount($entity, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM);
    }

    private function countActiveLargeVehicles($entity)
    {
        if (!$entity->canHaveLargeVehicles()) {
            return 0;
        }

        return $this->getActiveVehiclesCount($entity, Entity\Vehicle\Vehicle::PSV_TYPE_LARGE);
    }

    private function getSmallVehicles($entity, $includeRemoved = false)
    {
        return $this->getVehicles($entity, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL, $includeRemoved);
    }

    private function getMediumVehicles($entity, $includeRemoved = false)
    {
        return $this->getVehicles($entity, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM, $includeRemoved);
    }

    private function getLargeVehicles($entity, $includeRemoved = false)
    {
        if (!$entity->canHaveLargeVehicles()) {
            return [];
        }

        return $this->getVehicles($entity, Entity\Vehicle\Vehicle::PSV_TYPE_LARGE, $includeRemoved);
    }

    private function getVehicles($entity, $type, $includeRemoved = false)
    {
        $licenceVehicles = $this->licenceVehicleRepo->getPsvVehiclesByType($entity, $type, $includeRemoved);

        return (new ResultList($licenceVehicles, ['vehicle']))->serialize();
    }

    private function getActiveVehiclesCount($entity, $type)
    {
        return $this->licenceVehicleRepo->getPsvVehiclesByType($entity, $type)->count();
    }
}
