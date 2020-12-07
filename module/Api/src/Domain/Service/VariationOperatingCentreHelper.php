<?php

/**
 * Variation Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
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

    public function getListDataForApplication(Application $application, $query = null)
    {
        $aocs = $this->aocRepo->fetchByApplicationIdForOperatingCentres($application->getId());
        $locs = $this->locRepo->fetchByLicenceIdForOperatingCentres($application->getLicence()->getId());

        $aocData = [];
        $locData = [];

        $sort = null;
        $order = 'ASC';
        if (!is_null($query)) {
            $sort = $query->getSort();
            $order = $query->getOrder();
        }

        foreach ($aocs as $aoc) {
            $aoc['source'] = 'A';
            $aoc['id'] = 'A' . $aoc['id'];
            $aoc['sort'] = $this->addCustomSort($aoc, $sort);

            $aocData[$aoc['operatingCentre']['id']] = $aoc;
        }

        foreach ($locs as $loc) {
            $loc['source'] = 'L';
            $loc['id'] = 'L' . $loc['id'];
            $loc['sort'] = $this->addCustomSort($loc, $sort);

            $locData[$loc['operatingCentre']['id']] = $loc;
        }
        return $this->updateAndFilterTableData($locData, $aocData, $order);
    }

    private function updateAndFilterTableData($licenceData, $applicationData, $order)
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
        if ($order === 'ASC') {
            usort(
                $mergedData,
                function ($val1, $val2) {
                    return strcmp($val1['sort'], $val2['sort']);
                }
            );
        } else {
            usort(
                $mergedData,
                function ($val1, $val2) {
                    return strcmp($val2['sort'], $val1['sort']);
                }
            );
        }

        return $mergedData;
    }

    /**
     * Custom sorting for variation operating centres
     *
     * @param array $oc
     * @param $sort
     * @return array
     */
    private function addCustomSort($oc, $sort)
    {
        switch ($sort) {
            case 'noOfVehiclesRequired':
            case 'noOfTrailersRequired':
                $value = (int) $oc[$sort];
                break;
            case 'createdOn':
                $value = $oc[$sort];
                break;
            case 'adr':
                $value = strtolower(
                    $oc['operatingCentre']['address']['addressLine1'] .
                    $oc['operatingCentre']['address']['addressLine2'] .
                    $oc['operatingCentre']['address']['addressLine3'] .
                    $oc['operatingCentre']['address']['addressLine4'] .
                    $oc['operatingCentre']['address']['town']
                );
                break;
            case 'lastModifiedOn':
                $value = is_null($oc['lastModifiedOn']) ? $oc['createdOn'] : $oc['lastModifiedOn'];
                break;
            default:
                $value = $oc['operatingCentre']['id'];
        }
        return $value;
    }
}
