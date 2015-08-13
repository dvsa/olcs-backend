<?php

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehicles extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['LicenceVehicle'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity\Licence\Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        $small = $this->getSmallVehicles($licence, $query->getIncludeRemoved());
        $medium = $this->getMediumVehicles($licence, $query->getIncludeRemoved());
        $large = $this->getLargeVehicles($licence, $query->getIncludeRemoved());

        $smallCount = count($small);
        $mediumCount = count($medium);
        $largeCount = count($large);

        $activeSmallCount = $this->countActiveSmallVehicles($licence);
        $activeMediumCount = $this->countActiveMediumVehicles($licence);
        $activeLargeCount = $this->countActiveLargeVehicles($licence);

        return $this->result(
            $licence,
            [],
            [
                'showSmallTable' => $licence->shouldShowSmallTable($smallCount),
                'showMediumTable' => $licence->shouldShowMediumTable($mediumCount),
                'showLargeTable' => $licence->shouldShowLargeTable($largeCount),
                'smallAuthExceeded' => $licence->isSmallAuthExceeded($activeSmallCount),
                'mediumAuthExceeded' => $licence->isMediumAuthExceeded($activeMediumCount),
                'largeAuthExceeded' => $licence->isLargeAuthExceeded($activeLargeCount),
                'availableSmallSpaces' => $licence->getAvailableSmallSpaces($activeSmallCount),
                'availableMediumSpaces' => $licence->getAvailableMediumSpaces($activeMediumCount),
                'availableLargeSpaces' => $licence->getAvailableLargeSpaces($activeLargeCount),
                'small' => $small,
                'medium' => $medium,
                'large' => $large,
                'total' => $smallCount + $mediumCount + $largeCount,
                'canTransfer' => !$licence->getOtherActiveLicences()->isEmpty(),
                'hasBreakdown' => $licence->hasPsvBreakdown()
            ]
        );
    }

    private function countActiveSmallVehicles(Entity\Licence\Licence $licence)
    {
        return $this->getActiveVehiclesCount($licence, Entity\Vehicle\Vehicle::PSV_TYPE_SMALL);
    }

    private function countActiveMediumVehicles(Entity\Licence\Licence $licence)
    {
        return $this->getActiveVehiclesCount($licence, Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM);
    }

    private function countActiveLargeVehicles(Entity\Licence\Licence $licence)
    {
        if (!$licence->canHaveLargeVehicles()) {
            return 0;
        }

        return $this->getActiveVehiclesCount($licence, Entity\Vehicle\Vehicle::PSV_TYPE_LARGE);
    }

    private function getSmallVehicles(Entity\Licence\Licence $licence, $includeRemoved)
    {
        return $this->getVehicles(
            $licence,
            Entity\Vehicle\Vehicle::PSV_TYPE_SMALL,
            $includeRemoved
        );
    }

    private function getMediumVehicles(Entity\Licence\Licence $licence, $includeRemoved)
    {
        return $this->getVehicles(
            $licence,
            Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM,
            $includeRemoved
        );
    }

    private function getLargeVehicles(Entity\Licence\Licence $licence, $includeRemoved)
    {
        if (!$licence->canHaveLargeVehicles()) {
            return [];
        }

        return $this->getVehicles(
            $licence,
            Entity\Vehicle\Vehicle::PSV_TYPE_LARGE,
            $includeRemoved
        );
    }

    private function getVehicles(Entity\Licence\Licence $licence, $type, $includeRemoved)
    {
        return $this->resultList(
            $this->getRepo('LicenceVehicle')->getPsvVehiclesByType(
                $licence,
                $type,
                $includeRemoved
            ),
            ['vehicle']
        );
    }

    private function getActiveVehiclesCount(Entity\Licence\Licence $licence, $type)
    {
        return $this->getRepo('LicenceVehicle')->getPsvVehiclesByType($licence, $type)->count();
    }
}
