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

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehicles extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['LicenceVehicle'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity\Application\Application $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $small = $this->getSmallVehicles($application, $query->getIncludeRemoved());
        $medium = $this->getMediumVehicles($application, $query->getIncludeRemoved());
        $large = $this->getLargeVehicles($application, $query->getIncludeRemoved());

        $smallCount = count($small);
        $mediumCount = count($medium);
        $largeCount = count($large);
        return $this->result(
            $application,
            [],
            [
                'showSmallTable' => $application->shouldShowSmallTable($smallCount),
                'showMediumTable' => $application->shouldShowMediumTable($mediumCount),
                'showLargeTable' => $application->shouldShowLargeTable($largeCount),
                'smallAuthExceeded' => $application->isSmallAuthExceeded($smallCount),
                'mediumAuthExceeded' => $application->isMediumAuthExceeded($mediumCount),
                'largeAuthExceeded' => $application->isLargeAuthExceeded($largeCount),
                'availableSmallSpaces' => $application->getAvailableSmallSpaces($smallCount),
                'availableMediumSpaces' => $application->getAvailableMediumSpaces($mediumCount),
                'availableLargeSpaces' => $application->getAvailableLargeSpaces($largeCount),
                'small' => $small,
                'medium' => $medium,
                'large' => $large,
                'total' => $smallCount + $mediumCount + $largeCount,
                'canTransfer' => false,
                // We have to have a (~nervous~) breakdown before we can get to application version
                'hasBreakdown' => $this->hasBreakdown($application),
            ]
        );
    }

    protected function hasBreakdown(Entity\Application\Application $application)
    {
        return false;
    }

    private function getSmallVehicles(Entity\Application\Application $application, $includeRemoved)
    {
        return $this->getVehicles(
            $application,
            Entity\Vehicle\Vehicle::PSV_TYPE_SMALL,
            $includeRemoved
        );
    }

    private function getMediumVehicles(Entity\Application\Application $application, $includeRemoved)
    {
        return $this->getVehicles(
            $application,
            Entity\Vehicle\Vehicle::PSV_TYPE_MEDIUM,
            $includeRemoved
        );
    }

    private function getLargeVehicles(Entity\Application\Application $application, $includeRemoved)
    {
        if (!$application->canHaveLargeVehicles()) {
            return [];
        }

        return $this->getVehicles(
            $application,
            Entity\Vehicle\Vehicle::PSV_TYPE_LARGE,
            $includeRemoved
        );
    }

    private function getVehicles(Entity\Application\Application $application, $type, $includeRemoved)
    {
        return $this->resultList(
            $this->getRepo('LicenceVehicle')->getPsvVehiclesByType(
                $application,
                $type,
                $includeRemoved
            ),
            ['vehicle']
        );
    }
}
