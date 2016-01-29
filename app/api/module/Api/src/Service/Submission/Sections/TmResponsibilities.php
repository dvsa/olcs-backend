<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;

/**
 * Class TmResponsibilities
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class TmResponsibilities extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $applications = [];
        $licences = [];

        if (!empty($case->getTransportManager()) && !empty($case->getTransportManager()->getTmApplications())) {
            $tmApplications = $case->getTransportManager()->getTmApplications();
            /** @var TransportManagerApplication $entity */
            foreach ($tmApplications as $entity) {
                $applications[] = $this->extractResponsibilityData($case, $entity);
            }
        }

        if (!empty($case->getTransportManager()) && !empty($case->getTransportManager()->getTmLicences())) {
            $tmLicences = $case->getTransportManager()->getTmLicences();
            /** @var TransportManagerLicence $entity */
            foreach ($tmLicences as $entity) {
                $licences[] = $this->extractResponsibilityData($case, $entity);
            }
        }

        return [
            'data' => [
                'tables' => [
                    'applications' => $applications,
                    'licences' => $licences
                ]
            ]
        ];
    }

    /**
     * Method to extract the row data. Most is common to both tmApplications and tmLicence
     *
     * @param $case
     * @param $entity
     * @return array
     */
    private function extractResponsibilityData($case, $entity)
    {
        $thisRow = array();

        $thisRow['id'] = $entity->getId();
        $thisRow['version'] = $entity->getVersion();
        $thisRow['managerType'] = $case->getTransportManager()->getTmType()->getDescription();
        $thisRow['noOpCentres'] = $entity->getOperatingCentres()->count();
        $thisRow['hrsPerWeek'] = $entity->getTotalWeeklyHours();

        if ($entity instanceof TransportManagerApplication) {

            /** @var TransportManagerApplication $entity */
            $application = !empty($entity->getApplication()) ? $entity->getApplication() : null;
            $applicationLicence = !empty($application->getLicence()) ? $application->getLicence() : null;
            $applicationLicenceOrganisation = !empty($applicationLicence) ?
                $applicationLicence->getOrganisation() : null;

            $thisRow['applicationId'] = !empty($application) ? $application->getId() : '';
            $thisRow['licNo'] = !empty($applicationLicence) ? $applicationLicence->getLicNo() : '';
            $thisRow['organisationName'] = !empty($applicationLicenceOrganisation) ?
                $applicationLicenceOrganisation->getName() : '';
            $thisRow['status'] = $application->getStatus()->getDescription();

        } elseif ($entity instanceof TransportManagerLicence) {

            /** @var TransportManagerLicence $entity */
            $licence = $entity->getLicence();
            $organisation = !empty($licence) ?
                $licence->getOrganisation() : null;

            $thisRow['licenceId'] = !empty($licence) ? $licence->getId() : '';
            $thisRow['licNo'] = !empty($licence) ? $licence->getLicNo() : '';
            $thisRow['organisationName'] = !empty($organisation) ?
                $organisation->getName() : '';
            $thisRow['status'] = !empty($licence) ? $licence->getStatus()->getDescription() : '';

        }

        return $thisRow;
    }
}
