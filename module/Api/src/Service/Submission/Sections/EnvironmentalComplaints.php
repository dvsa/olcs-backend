<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint;

/**
 * Class EnvironmentalComplaints
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class EnvironmentalComplaints extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $iterator = $case->getEnvironmentalComplaints()->getIterator();

        $iterator->uasort(
            function ($a, $b) {
                if (null !== $a->getComplaintDate() &&
                    null !== $b->getComplaintDate()) {
                    return strtotime($a->getComplaintDate()->format('Ymd') -
                        strtotime($b->getComplaintDate()->format('Ymd')));
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
            $thisRow['ocAddress'] = $this->extractOperatingCentreData($entity->getOperatingCentres());
            $thisRow['closeDate'] = $entity->getClosedDate();
            $thisRow['status'] = !empty($entity->getStatus()) ? $entity->getStatus()->getDescription() : '';

            $data[] = $thisRow;

            $complaints->next();
        }

        return [
            'data' => [
                'tables' => [
                    'environmental-complaints' => $data
                ]
            ]
        ];
    }

    private function extractOperatingCentreData($operatingCentres = [])
    {
        $operatingCentreData = [];
        foreach ($operatingCentres as $operatingCentre) {
            $operatingCentreData[] = [
               'address' => $operatingCentre->getAddress()->toArray()
            ];
        }
        return $operatingCentreData;
    }
}
