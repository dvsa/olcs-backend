<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Conviction;

/**
 * Class ConvictionFpnOffenceHistory
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class ConvictionFpnOffenceHistory extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $convictions = $case->getConvictions();

        $data = [];

        /** @var Conviction $entity */
        foreach ($convictions as $entity) {
            $thisRow = array();
            $thisRow['id'] = $entity->getId();
            $thisRow['version'] = $entity->getVersion();
            $thisRow['offenceDate'] = $this->formatDate($entity->getOffenceDate());
            $thisRow['convictionDate'] = $this->formatDate($entity->getConvictionDate());
            $thisRow['defendantType'] = $entity->getDefendantType()->getDescription();
            $thisRow['name'] = $this->determineName($entity);
            $thisRow['categoryText'] = $entity->getCategoryText();
            $thisRow['court'] = $entity->getCourt();
            $thisRow['penalty'] = $entity->getPenalty();
            $thisRow['msi'] = $entity->getMsi();
            $thisRow['isDeclared'] = $entity->getIsDeclared();
            $thisRow['isDealtWith'] = $entity->getIsDealtWith();

            $data[] = $thisRow;
        }

        return [
            'data' => [
                'tables' => [
                    'conviction-fpn-offence-history' => $data
                ],
                'text' => $case->getConvictionNote()
            ]
        ];
    }

    /**
     * Method to extract the name depending on Conviction defendant
     * @param Conviction $entity
     * @return string
     */
    private function determineName(Conviction $entity)
    {
        if ($entity->getDefendantType()->getId() == Conviction::DEFENDANT_TYPE_ORGANISATION) {
            return $entity->getOperatorName();
        } else {
            return $entity->getPersonFirstname() . ' ' . $entity->getPersonLastname();
        }
    }
}
