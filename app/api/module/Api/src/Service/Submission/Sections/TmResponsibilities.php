<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Statement;
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

        if (
            !empty($case->getTransportManager()) &&
            !empty($case->getTransportManager()->getTmApplications()
            )) {

            $tmApplications = $case->getTransportManager()->getTmApplications();

            for ($i = 0; $i < count($tmApplications); $i++) {
                /** @var TransportManagerApplication $entity */
                $entity = $tmApplications->current();

                $applications[] = $this->extractResponsibilityData($case, $entity);;

                $tmApplications->next();
            }
        }

        if (
            !empty($case->getTransportManager()) &&
            !empty($case->getTransportManager()->getTmLicences()
            )) {

            $tmLicences = $case->getTransportManager()->getTmLicences();

            for ($i = 0; $i < count($tmLicences); $i++) {
                /** @var TransportManagerLicence $entity */
                $entity = $tmLicences->current();

                $licences[] = $this->extractResponsibilityData($case, $entity);;

                $tmLicences->next();
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
        $thisRow['noOpCentres'] = count($entity->getOperatingCentres());
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
