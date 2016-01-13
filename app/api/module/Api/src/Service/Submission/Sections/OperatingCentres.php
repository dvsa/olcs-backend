<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;

/**
 * Class OperatingCentres
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class OperatingCentres extends AbstractSection
{
    /**
     * Generate licence operating centres with sorted address
     * 
     * @param CasesEntity $case
     * @param \ArrayObject|null $context
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $licence = $case->getLicence();

        $licenceOperatingCentres = $this->extractSortedOperatingCentres($licence);

        $data = [];
        for ($i=0; $i<count($licenceOperatingCentres); $i++) {
            /** @var OperatingCentre $operatingCentre */
            $operatingCentre = $licenceOperatingCentres->current()->getOperatingCentre();

            $thisEntity = array();

            if (!(empty($operatingCentre))) {

                $thisEntity['id'] = $operatingCentre->getId();
                $thisEntity['version'] = $operatingCentre->getVersion();
                $thisEntity['totAuthVehicles'] = $licenceOperatingCentres->current()->getNoOfVehiclesRequired();
                $thisEntity['totAuthTrailers'] = $licenceOperatingCentres->current()->getNoOfTrailersRequired();
                if (empty($operatingCentre->getAddress())) {
                    $thisEntity['OcAddress'] = [];
                } else {
                    $thisEntity['OcAddress'] = $operatingCentre->getAddress()->toArray();
                }
                $data[] = $thisEntity;
            }

            $licenceOperatingCentres->next();
        }

        return [
            'data' => [
                'tables' => [
                    'operating-centres' => $data
                ]
            ]
        ];
    }

    /**
     * Sorted operating centres by address postcode
     *
     * @param $licence
     * @return ArrayCollection
     */
    private function extractSortedOperatingCentres($licence)
    {
        $sorted = [];
        if (!empty($licence->getOperatingCentres())) {
            $iterator = $licence->getOperatingCentres()->getIterator();
            $iterator->uasort(
                function ($a, $b) {
                    if (null !== $a->getOperatingCentre()->getAddress()->getPostcode() &&
                        null !== $b->getOperatingCentre()->getAddress()->getPostcode()) {
                        return strcmp(
                            $a->getOperatingCentre()->getAddress()->getPostcode(),
                            $b->getOperatingCentre()->getAddress()->getPostcode()
                        );
                    }
                }
            );
            $sorted = iterator_to_array($iterator);
        }

        return new ArrayCollection($sorted);

    }
}
