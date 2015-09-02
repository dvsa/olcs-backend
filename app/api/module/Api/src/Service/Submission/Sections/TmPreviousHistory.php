<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Application\PreviousConviction;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;

/**
 * Class TmPreviousHistory
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class TmPreviousHistory extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $convictionPenaltyData = [];
        $revokedCurtailedSuspendedLicences = [];

        if (!empty($case->getTransportManager())) {

            $previousConvictions = $case->getTransportManager()->getPreviousConvictions();

            for ($i = 0; $i < count($previousConvictions); $i++) {
                /** @var PreviousConviction $entity */
                $entity = $previousConvictions->current();

                $thisRow = array();
                $thisRow['id'] = $entity->getId();
                $thisRow['version'] = $entity->getVersion();
                $thisRow['offence'] = $entity->getCategoryText();
                $thisRow['convictionDate'] = $this->formatDate($entity->getConvictionDate());
                $thisRow['courtFpn'] = $entity->getCourtFpn();
                $thisRow['penalty'] = $entity->getPenalty();

                $convictionPenaltyData[] = $thisRow;

                $previousConvictions->next();
            }

            $otherLicences = $case->getTransportManager()->getOtherLicences();

            for ($i = 0; $i < count($otherLicences); $i++) {
                /** @var OtherLicence $entity */
                $entity = $otherLicences->current();

                $thisRow = array();
                $thisRow['id'] = $entity->getId();
                $thisRow['version'] = $entity->getVersion();
                $thisRow['licNo'] = $entity->getLicNo();
                $thisRow['holderName'] = $entity->getHolderName();

                $revokedCurtailedSuspendedLicences[] = $thisRow;

                $previousConvictions->next();
            }

        }

        return [
            'data' => [
                'tables' => [
                    'convictions-and-penalties' => $convictionPenaltyData,
                    'revoked-curtailed-suspended-licences' => $revokedCurtailedSuspendedLicences
                ]
            ]
        ];
    }
}
