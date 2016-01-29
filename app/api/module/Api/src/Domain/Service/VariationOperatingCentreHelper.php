<?php

/**
 * Variation Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Domain\Repository\LicenceOperatingCentre;

/**
 * Variation Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentreHelper implements FactoryInterface
{
    /**
     * @var ApplicationOperatingCentre
     */
    protected $aocRepo;

    /**
     * @var LicenceOperatingCentre
     */
    protected $locRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoSm = $serviceLocator->get('RepositoryServiceManager');

        $this->aocRepo = $repoSm->get('ApplicationOperatingCentre');
        $this->locRepo = $repoSm->get('LicenceOperatingCentre');

        return $this;
    }

    public function getListDataForApplication(Application $application)
    {
        $aocs = $this->aocRepo->fetchByApplicationIdForOperatingCentres($application->getId());
        $locs = $this->locRepo->fetchByLicenceIdForOperatingCentres($application->getLicence()->getId());

        $aocData = [];
        $locData = [];

        foreach ($aocs as $aoc) {

            $aoc['source'] = 'A';
            $aoc['id'] = 'A' . $aoc['id'];
            $aoc['sort'] = $aoc['operatingCentre']['id'];

            $aocData[$aoc['operatingCentre']['id']] = $aoc;
        }

        foreach ($locs as $loc) {

            $loc['source'] = 'L';
            $loc['id'] = 'L' . $loc['id'];
            $loc['sort'] = $loc['operatingCentre']['id'];

            $locData[$loc['operatingCentre']['id']] = $loc;
        }

        return $this->updateAndFilterTableData($locData, $aocData);
    }

    private function updateAndFilterTableData($licenceData, $applicationData)
    {
        $data = [];

        foreach ($licenceData as $ocId => $row) {
            if (!isset($applicationData[$ocId])) {
                $row['action'] = 'E';
                $data[] = $row;
            } elseif ($applicationData[$ocId]['action'] === 'U') {
                $row['action'] = 'C';
                $data[] = $row;
            }
        }

        $mergedData = array_merge($data, $applicationData);

        usort(
            $mergedData,
            function ($val1, $val2) {
                return strcmp($val1['sort'], $val2['sort']);
            }
        );

        return $mergedData;
    }
}
