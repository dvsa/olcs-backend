<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Statement;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Class Oppositions
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class Oppositions extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $iterator = $case->getOppositions()->getIterator();

        $iterator->uasort(
            function ($a, $b) {
            /** @var Opposition $a */
            /** @var Opposition $b */
                if (null !== $a->getOppositionType() &&
                    null !== $b->getOppositionType()) {
                    return strnatcmp(
                        $b->getOppositionType()->getDescription(),
                        $a->getOppositionType()->getDescription()
                    );
                }
            }
        );

        $iterator->uasort(
            function ($a, $b) {
                /** @var Opposition $a */
                /** @var Opposition $b */
                if (null !== $a->getRaisedDate() &&
                    null !== $b->getRaisedDate()) {
                    return strtotime($b->getRaisedDate()->format('Ymd')) -
                    strtotime($a->getRaisedDate()->format('Ymd'));
                }
            }
        );

        $oppositions = new ArrayCollection(iterator_to_array($iterator));

        $data = [];
        for ($i = 0; $i < count($oppositions); $i++) {
            /** @var Opposition $entity */
            $entity = $oppositions->current();

            $thisRow = array();
            $thisRow['id'] = $entity->getId();
            $thisRow['version'] = $entity->getVersion();
            $thisRow['dateReceived'] = $entity->getRaisedDate();
            $thisRow['oppositionType'] = $entity->getOppositionType()->getDescription();
            $thisRow['contactName'] = $this->extractPerson($entity->getOpposer()->getContactDetails());

            /** @var RefData $ground */
            foreach ($entity->getGrounds() as $ground) {
                $thisRow['grounds'][] = $ground->getDescription();
            }
            $thisRow['isValid'] = $entity->getIsValid();
            $thisRow['isCopied'] = $entity->getIsCopied();
            $thisRow['isInTime'] = $entity->getIsInTime();
            $thisRow['isPublicInquiry'] = $entity->getIsPublicInquiry();
            $thisRow['isWithdrawn'] = $entity->getIsWithdrawn();

            $data[] = $thisRow;

            $oppositions->next();
        }

        return [
            'data' => [
                'tables' => [
                    'oppositions' => $data
                ]
            ]
        ];
    }
}
