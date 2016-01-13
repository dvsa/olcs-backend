<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruImposed;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruRequested;

/**
 * Class Penalties
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class Penalties extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $tables['applied-penalties'] = [];
        $tables['imposed-penalties'] = [];
        $tables['requested-penalties'] = [];

        if (isset($case->getSeriousInfringements()[0]) &&
            ($case->getSeriousInfringements()[0] instanceof SeriousInfringement)) {
            /** @var SeriousInfringement $si */
            $si = $case->getSeriousInfringements()[0];

            /** @var SiPenalty $appliedPenalty */
            foreach ($si->getAppliedPenalties() as $appliedPenalty) {
                $penalty = array();
                $penalty['id'] = $appliedPenalty->getId();
                $penalty['version'] = $appliedPenalty->getVersion();
                $penalty['penaltyType'] = $appliedPenalty->getSiPenaltyType()->getDescription();
                $penalty['startDate'] = $this->formatDate($appliedPenalty->getStartDate());
                $penalty['endDate'] = $this->formatDate($appliedPenalty->getEndDate());
                $penalty['imposed'] = $appliedPenalty->getImposed();
                $tables['applied-penalties'][] = $penalty;
            }

            /** @var SiPenaltyErruImposed $imposedPenalty */
            foreach ($si->getImposedErrus() as $imposedPenalty) {
                $penalty = array();
                $penalty['id'] = $imposedPenalty->getId();
                $penalty['version'] = $imposedPenalty->getVersion();
                $penalty['finalDecisionDate'] = $this->formatDate($imposedPenalty->getFinalDecisionDate());
                $penalty['penaltyType'] = $imposedPenalty->getSiPenaltyImposedType()->getDescription();
                $penalty['startDate'] = $this->formatDate($imposedPenalty->getStartDate());
                $penalty['endDate'] = $this->formatDate($imposedPenalty->getEndDate());
                $penalty['executed'] = $imposedPenalty->getExecuted();
                $tables['imposed-penalties'][] = $penalty;
            }

            /** @var SiPenaltyErruRequested $requestedPenalty */
            foreach ($si->getRequestedErrus() as $requestedPenalty) {
                $penalty = array();
                $penalty['id'] = $requestedPenalty->getId();
                $penalty['version'] = $requestedPenalty->getVersion();
                $penalty['penaltyType'] = $requestedPenalty->getSiPenaltyRequestedType()->getDescription();
                $penalty['duration'] = $requestedPenalty->getDuration();
                $tables['requested-penalties'][] = $penalty;
            }
        }

        return [
            'data' => [
                'overview' => $this->extractOverview($case),
                'tables' => $tables,
                'text' => $case->getPenaltiesNote()
            ]
        ];
    }

    /**
     * Method to extract SI Overview data
     * @param CasesEntity $case
     * @return array
     */
    private function extractOverview(CasesEntity $case)
    {
        $siData = [
            'vrm' => $case->getErruVrm(),
            'transportUndertakingName' => $case->getErruTransportUndertakingName(),
            'originatingAuthority' => $case->getErruOriginatingAuthority(),
            'infringementId' => '',
            'notificationNumber' => '',
            'infringementDate' => '',
            'checkDate' => '',
            'category' => '',
            'categoryType' => '',
            'memberState' => ''
        ];

        if (isset($case->getSeriousInfringements()[0]) &&
            ($case->getSeriousInfringements()[0] instanceof SeriousInfringement)) {
            /** @var SeriousInfringement $si */
            $si = $case->getSeriousInfringements()[0];

            $siData['infringementId'] = $si->getId();
            $siData['notificationNumber'] = $si->getNotificationNumber();
            $siData['infringementDate'] = $this->formatDate($si->getInfringementDate());
            $siData['checkDate'] = $this->formatDate($si->getCheckDate());
            $siData['category'] = $si->getSiCategory()->getDescription();
            $siData['categoryType'] = $si->getSiCategoryType()->getDescription();
            $siData['memberState'] =
                !empty($si->getMemberStateCode()) ? $si->getMemberStateCode()->getCountryDesc() : '';
        }

        return $siData;
    }
}
