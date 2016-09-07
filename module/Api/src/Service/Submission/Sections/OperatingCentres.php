<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity;
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
     * @param CasesEntity $case Case relating to the submission
     *
     * @return array Data array containing information for the submission section
     */
    public function generateSection(CasesEntity $case)
    {
        $applicationOperatingCentres = new ArrayCollection();
        $licence = $case->getLicence();
        $licenceOperatingCentres = $licence->getOperatingCentres();

        if ($case->getCaseType()->getId() === CasesEntity::APP_CASE_TYPE) {
            $applicationOperatingCentres = $case->getApplication()->getOperatingCentres();
        }

        $allOperatingCentres = $this->extractSortedOperatingCentres(
            new ArrayCollection(
                array_merge(
                    $licenceOperatingCentres->toArray(),
                    $applicationOperatingCentres->toArray()
                )
            )
        );

        $data = [];
        for ($i = 0, $n = count($allOperatingCentres); $i < $n; $i++) {
            /** @var OperatingCentre $operatingCentre */
            $operatingCentre = $allOperatingCentres->current()->getOperatingCentre();

            $thisEntity = array();

            if (!(empty($operatingCentre))) {

                $thisEntity['id'] = $operatingCentre->getId();
                $thisEntity['version'] = $operatingCentre->getVersion();
                $thisEntity['totAuthVehicles'] = $allOperatingCentres->current()->getNoOfVehiclesRequired();
                $thisEntity['totAuthTrailers'] = $allOperatingCentres->current()->getNoOfTrailersRequired();
                if (empty($operatingCentre->getAddress())) {
                    $thisEntity['OcAddress'] = [];
                } else {
                    $thisEntity['OcAddress'] = $operatingCentre->getAddress()->toArray();
                }
                $data[] = $thisEntity;
            }

            $allOperatingCentres->next();
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
     * @param ArrayCollection $ocArray Array of Operating Centres
     *
     * @return ArrayCollection
     */
    private function extractSortedOperatingCentres(ArrayCollection $ocArray)
    {
        $sorted = [];
        if (!empty($ocArray)) {
            $iterator = $ocArray->getIterator();

            $iterator->uasort(
                function ($a, $b) {
                    /** @var Entity\Application\ApplicationOperatingCentre | Entity\Licence\LicenceOperatingCentre $a */
                    /** @var Entity\Application\ApplicationOperatingCentre | Entity\Licence\LicenceOperatingCentre $b */
                    $aAddress = $a->getOperatingCentre()->getAddress();
                    $aPostCode = ($aAddress !== null ? $aAddress->getPostcode() : '');

                    $bAddress = $b->getOperatingCentre()->getAddress();
                    $bPostCode = ($bAddress !== null ? $bAddress->getPostcode() : '');

                    return strcmp($aPostCode, $bPostCode);
                }
            );
            $sorted = iterator_to_array($iterator);
        }

        return new ArrayCollection($sorted);

    }
}
