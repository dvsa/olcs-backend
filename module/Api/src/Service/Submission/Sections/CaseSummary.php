<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * Class CaseSummary
 * @package Dvsa\Olcs\Api\Service\Submission\Section\CaseSummary
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class CaseSummary extends AbstractSection
{
    public function generateSection(CasesEntity $case, \ArrayObject $context = null)
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
        $data['organisationName'] = !empty($organisation) ? $organisation->getName() : '';
        $data['isMlh'] = !empty($organisation) ? $organisation->isMlh() : '';
        $data['organisationType'] = !empty($organisation) ? $organisation->getType()->getDescription() : '';

        $data['businessType'] = $organisation->getNatureOfBusiness();

        // licence data
        $data['licNo'] = !empty($licence) ? $licence->getLicNo() : '';
        $data['licenceStartDate'] = !empty($licence) ? $licence->getInForceDate() : '';
        $data['licenceType'] = !empty($licence) ? $licence->getLicenceType()->getDescription() : '';
        $data['goodsOrPsv'] = !empty($licence) ? $licence->getGoodsOrPsv()->getDescription() : '';
        $data['licenceStatus'] = !empty($licence) ? $licence->getStatus()->getDescription() : '';
        $data['totAuthorisedVehicles'] = !empty($licence) ? $licence->getTotAuthVehicles() : '';
        $data['totAuthorisedTrailers'] = !empty($licence) ? $licence->getTotAuthTrailers() : '';
        $data['vehiclesInPossession'] = !empty($licence) ? $licence->getTotAuthTrailers() : '';
        $data['vehiclesInPossession'] = !empty($licence) ? $licence->getActiveVehiclesCount() : '';
        $data['trailersInPossession'] = !empty($licence) ? $licence->getTotAuthTrailers() : '';

        // application data
        $data['serviceStandardDate'] = !empty($application) ? $application->getTargetCompletionDate() : '';

        return ['data' => ['overview' => $data]];
    }
}
