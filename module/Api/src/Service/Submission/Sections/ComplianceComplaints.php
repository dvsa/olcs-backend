<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint;

/**
 * Class ComplianceComplaints
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class ComplianceComplaints extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {

        $iterator = $case->getComplianceComplaints()->getIterator();

        $iterator->uasort(
            function ($a, $b) {
                if ($a->getComplaintDate() instanceof \DateTime &&
                    $b->getComplaintDate() instanceof \DateTime) {
                    return strtotime(
                        $a->getComplaintDate()->format('Ymd') - strtotime(
                            $b->getComplaintDate()->format('Ymd')
                        )
                    );
                }
            }
        );

        $complaints = new ArrayCollection(iterator_to_array($iterator));

        $data = [];
        for ($i = 0; $i < count($complaints); $i++) {

            /** @var Complaint $entity */
            $entity = $complaints->current();

            $thisRow = array();
            $thisRow['id'] = $entity->getId();
            $thisRow['version'] = $entity->getVersion();
            $personData = $this->extractPerson($entity->getComplainantContactDetails());
            $thisRow['complainantForename'] = $personData['forename'];
            $thisRow['complainantFamilyName'] = $personData['familyName'];
            $thisRow['description'] = $entity->getDescription();
            $thisRow['complaintDate'] = $entity->getComplaintDate();

            $data[] = $thisRow;

            $complaints->next();
        }

        return [
            'data' => [
                'tables' => [
                    'compliance-complaints' => $data
                ]
            ]
        ];
    }
}
