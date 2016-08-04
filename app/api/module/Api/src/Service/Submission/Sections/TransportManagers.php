<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\Tm\AbstractTmQualification;
use Dvsa\Olcs\Api\Entity\Tm\TmQualification;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;

/**
 * Class TransportManagers
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class TransportManagers extends AbstractSection
{
    /**
     * Final results table array
     * @var array
     */
    private $dataToReturnArray = array();

    /**
     * Generates the TransportManagers section for submissions.
     *
     * All the Transport managers listed on the operator's applications for that licence which have a status of under
     * consideration and the operator's licence that is relevant to the case.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $caseLicence = $case->getLicence();

        // Attach all TMs on licence applications
        $licenceApplicationsUnderConsideration = $caseLicence->getApplicationsByStatus(
            [Application::APPLICATION_STATUS_UNDER_CONSIDERATION]
        );

        /** @var Application $application */
        foreach ($licenceApplicationsUnderConsideration as $application) {
            /** @var TransportManagerApplication $transportManagerApplication */

            foreach ($application->getTransportManagers() as $transportManagerApplication) {
                $this->extractTmData(
                    $transportManagerApplication->getTransportManager(),
                    $application->getLicence()->getLicNo()
                );
            }

        }

        // Attach all TMs associated with the licence
        $licenceTms = $caseLicence->getTmLicences();

        // extract case licence TM data
        if (!empty($licenceTms)) {
            /** @var TransportManagerLicence $tmLicence */
            foreach ($licenceTms as $tmLicence) {
                $this->extractTmData(
                    $tmLicence->getTransportManager(),
                    $caseLicence->getLicNo()
                );
            }
        }

        return [
            'data' => [
                'tables' => [
                    'transport-managers' => $this->dataToReturnArray
                ]
            ]
        ];
    }

    /**
     * Method to extract the required data for a transport manager array
     * @param TransportManager $transportManager
     * @param $licenceNo
     */
    private function extractTmData(TransportManager $transportManager, $licenceNo)
    {
        $thisRow = array();
        $thisRow['licNo'] = $licenceNo;
        $thisRow['id'] = $transportManager->getId();
        $thisRow['version'] = $transportManager->getVersion();
        $thisRow['tmType'] = !empty($transportManager->getTmType()) ?
            $transportManager->getTmType()->getDescription() : '';

        $thisRow += $this->extractPerson($transportManager->getHomeCd());

        $thisRow['qualifications'] = $this->extractQualificationsData($transportManager);

        $thisRow['otherLicences'] = $this->extractOtherLicenceData($transportManager);

        $this->dataToReturnArray[$transportManager->getId()] = $thisRow;
    }

    /**
     * Extract qualification descriptions as array
     * @param TransportManager $transportManager
     * @return array
     */
    private function extractQualificationsData(TransportManager $transportManager)
    {
        $qualificationData = [];
        /** @var TmQualification $qualification */
        foreach ($transportManager->getQualifications() as $qualification) {
            $qualificationData[] = (null !== $qualification->getQualificationType()) ?
                $qualification->getQualificationType()->getDescription() : '';
        }
        return $qualificationData;
    }

    /**
     * Extract other licence data as array
     * @param TransportManager $transportManager
     * @return array
     */
    private function extractOtherLicenceData(TransportManager $transportManager)
    {
        $otherLicenceData = [];

        /** @var OtherLicence $otherLicence */
        foreach ($transportManager->getOtherLicences() as $otherLicence) {
            $thisOtherRow = array();
            $thisOtherRow['licNo'] = $otherLicence->getLicNo();
            $thisOtherRow['applicationId'] = (null !== $otherLicence->getApplication()) ?
            $otherLicence->getApplication()->getId() : '';
            $otherLicenceData[] = $thisOtherRow;
        }

        return $otherLicenceData;
    }
}
