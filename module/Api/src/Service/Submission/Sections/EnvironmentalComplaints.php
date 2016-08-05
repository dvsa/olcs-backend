<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;

/**
 * Class EnvironmentalComplaints
 *
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class EnvironmentalComplaints extends AbstractSection
{
    /**
     * Generate EnvironmentalComplaints Submission Section
     *
     * @param CasesEntity $case Case relating to the submission
     *
     * @return array Data array containing information for the submission section
     */
    public function generateSection(CasesEntity $case)
    {
        $iterator = $case->getEnvironmentalComplaints()->getIterator();

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
            $thisRow['ocAddress'] = $this->extractOperatingCentreData($entity->getOperatingCentres());
            $thisRow['closeDate'] = $this->formatDate($entity->getClosedDate());
            $thisRow['status'] = !empty($entity->getStatus()) ? $entity->getStatus()->getDescription() : '';

            $data[] = $thisRow;
        }

        return [
            'data' => [
                'tables' => [
                    'environmental-complaints' => $data
                ]
            ]
        ];
    }

    /**
     * Extract Operating centre data
     *
     * @param array $operatingCentres Array of operating centre entities
     *
     * @return array
     */
    private function extractOperatingCentreData($operatingCentres = [])
    {
        $operatingCentreData = [];
        /** @var OperatingCentre $operatingCentre */
        foreach ($operatingCentres as $operatingCentre) {
            $operatingCentreData[] = [
               'address' => $operatingCentre->getAddress()->toArray()
            ];
        }
        return $operatingCentreData;
    }
}
