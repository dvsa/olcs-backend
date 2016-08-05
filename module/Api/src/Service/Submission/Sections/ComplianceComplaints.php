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
     * @param CasesEntity $case Case relating to the submission
     *
     * @return array Data array containing information for the submission section
     */
    public function generateSection(CasesEntity $case)
    {
        $iterator = $case->getComplianceComplaints()->getIterator();

        $iterator->uasort(
            function ($a, $b) {
                if (($a->getComplaintDate() instanceof \DateTime) &&
                    ($b->getComplaintDate() instanceof \DateTime)) {
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
        /** @var Complaint $entity */
        foreach ($complaints as $entity) {
            $thisRow = array();
            $thisRow['id'] = $entity->getId();
            $thisRow['version'] = $entity->getVersion();
            $personData = $this->extractPerson($entity->getComplainantContactDetails());
            $thisRow['complainantForename'] = $personData['forename'];
            $thisRow['complainantFamilyName'] = $personData['familyName'];
            $thisRow['description'] = $entity->getDescription();
            $thisRow['complaintDate'] = $this->formatDate($entity->getComplaintDate());

            $data[] = $thisRow;
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
