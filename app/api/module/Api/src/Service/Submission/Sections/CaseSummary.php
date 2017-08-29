<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Class CaseSummary
 * @package Dvsa\Olcs\Api\Service\Submission\Section\CaseSummary
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class CaseSummary extends AbstractSection
{
    /**
     * Generate CaseSummary Submission Section
     *
     * @param CasesEntity $case Case relating to the submission
     *
     * @return array Data array containing information for the submission section
     */
    public function generateSection(CasesEntity $case)
    {
        $licence = $case->getLicence();
        $organisation = !empty($licence) ? $licence->getOrganisation() : '';
        $application = $case->getApplication();

        // case data
        $data = [
            'id' => $case->getId(),
            'caseType' => $case->getCaseType()->getDescription(),
            'ecmsNo' => $case->getEcmsNo(),
        ];

        // organisation data
        $data += $this->extractOrganisationData($organisation);

        // licence data
        $data += $this->extractLicenceData($licence);

        // application data
        $data['serviceStandardDate'] = !empty($application) ?
            $this->formatDate($application->getTargetCompletionDate()) : '';

        if (!empty($application)) {
            $data['licenceType'] = !empty($application->getLicenceType())
                ? $application->getLicenceType()->getDescription()
                : '';

            $data['licenceStartDate'] = !empty($data['licenceStartDate']) ? $data['licenceStartDate'] : 'N/A';

            $data['goodsOrPsv'] = !empty($application->getGoodsOrPsv())
                ? $application->getGoodsOrPsv()->getDescription()
                : '';
        }

        return ['data' => ['overview' => $data]];
    }

    /**
     * Extract licence data required for submission section from the licence
     *
     * @param null|Licence $licence Licence or null
     *
     * @return array
     */
    private function extractLicenceData($licence = null)
    {
        $licenceData = [];
        $licenceData['licNo'] = '';
        $licenceData['licenceStartDate'] = '';
        $licenceData['licenceType'] = '';
        $licenceData['goodsOrPsv'] = '';
        $licenceData['licenceStatus'] = '';
        $licenceData['totAuthorisedVehicles'] = '';
        $licenceData['totAuthorisedTrailers'] = '';
        $licenceData['vehiclesInPossession'] = '';
        $licenceData['vehiclesInPossession'] = '';
        $licenceData['trailersInPossession'] = '';

        if (!empty($licence) && $licence instanceof Licence) {
            $licenceData['licNo'] = $licence->getLicNo();
            $licenceData['licenceStartDate'] = $this->formatDate($licence->getInForceDate());
            $licenceData['licenceType'] = !empty($licence->getLicenceType()) ?
                $licence->getLicenceType()->getDescription() : '';
            $licenceData['goodsOrPsv'] =
                !empty($licence->getGoodsOrPsv()) ? $licence->getGoodsOrPsv()->getDescription() : '';
            $licenceData['licenceStatus'] =
                !empty($licence->getStatus()) ? $licence->getStatus()->getDescription() : '';
            $licenceData['totAuthorisedVehicles'] = $licence->getTotAuthVehicles();
            $licenceData['totAuthorisedTrailers'] = $licence->getTotAuthTrailers();
            $licenceData['vehiclesInPossession'] = $licence->getTotAuthTrailers();
            $licenceData['vehiclesInPossession'] = $licence->getActiveVehiclesCount();
            $licenceData['trailersInPossession'] = $licence->getTotAuthTrailers();
        }

        return $licenceData;
    }

    /**
     * Extract organisation data required for submission section
     *
     * @param null|Organisation $organisation Organisation entity
     *
     * @return array
     */
    private function extractOrganisationData($organisation = null)
    {
        $organisationData = [];
        $organisationData['organisationName'] = '';
        $organisationData['isMlh'] = '';
        $organisationData['organisationType'] = '';
        $organisationData['businessType'] = '';
        $organisationData['disqualificationStatus'] = '';

        if (!empty($organisation) && ($organisation instanceof Organisation)) {
            $organisationData['organisationName'] = $organisation->getName();
            $organisationData['isMlh'] = $organisation->isMlh();
            $organisationData['organisationType'] = $organisation->getType()->getDescription();
            $organisationData['businessType'] = $organisation->getNatureOfBusiness();
            $organisationData['disqualificationStatus'] = $organisation->getDisqualificationStatus();
        }

        return $organisationData;
    }
}
