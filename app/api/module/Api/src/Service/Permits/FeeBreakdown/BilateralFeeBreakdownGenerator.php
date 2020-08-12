<?php

namespace Dvsa\Olcs\Api\Service\Permits\FeeBreakdown;

use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;

class BilateralFeeBreakdownGenerator implements FeeBreakdownGeneratorInterface
{
    private const STANDARD_OR_CABOTAGE_KEY_MAPPINGS = [
        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 'standard',
        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 'cabotage'
    ];

    private const PERMIT_USAGE_KEY_MAPPINGS = [
        RefData::JOURNEY_SINGLE => 'single',
        RefData::JOURNEY_MULTIPLE => 'multiple'
    ];

    /** @var FeeTypeRepository */
    private $feeTypeRepo;

    /**
     * Create service instance
     *
     * @param FeeTypeRepository $feeTypeRepo
     *
     * @return BilateralFeeBreakdownGenerator
     */
    public function __construct(FeeTypeRepository $feeTypeRepo)
    {
        $this->feeTypeRepo = $feeTypeRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(IrhpApplication $irhpApplication)
    {
        $irhpPermitApplications = $irhpApplication->getIrhpPermitApplications();
        $rows = [];

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $permitUsage = $irhpPermitApplication->getBilateralPermitUsageSelection();

            $countryName = $irhpPermitApplication->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getCountry()
                ->getCountryDesc();

            $bilateralRequired = $irhpPermitApplication->getFilteredBilateralRequired();
            foreach ($bilateralRequired as $standardOrCabotage => $quantity) {
                $feePerPermit = $this->getFeePerPermit($irhpPermitApplication, $standardOrCabotage);
                $total = $feePerPermit * $quantity;

                $rows[] = [
                    'countryName' => $countryName,
                    'type' => $this->generateTranslationKey($standardOrCabotage, $permitUsage),
                    'quantity' => $quantity,
                    'total' => $total,
                ];
            }
        }

        // sort the rows by country name, but leave the order of rows within each country unchanged
        $distinctCountryNames = array_unique(array_column($rows, 'countryName'));
        sort($distinctCountryNames);

        $sortedRows = [];
        foreach ($distinctCountryNames as $countryName) {
            foreach ($rows as $row) {
                if ($row['countryName'] == $countryName) {
                    $sortedRows[] = $row;
                }
            }
        }

        return $sortedRows;
    }

    /**
     * Get the fee amount per permit
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param string $standardOrCabotage
     *
     * @return int
     */
    public function getFeePerPermit(IrhpPermitApplication $irhpPermitApplication, $standardOrCabotage)
    {
        $countryId = $irhpPermitApplication->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getCountry()
            ->getId();

        $feeTypes = [];
        $productReferences = $irhpPermitApplication->getBilateralFeeProductReferences($countryId, $standardOrCabotage);

        foreach ($productReferences as $productReference) {
            $feeTypes[] = $this->feeTypeRepo->getLatestByProductReference($productReference);
        }

        return $irhpPermitApplication->getBilateralFeePerPermit($feeTypes);
    }

    /**
     * Generate a translation key for use in the type column
     *
     * @param string $standardOrCabotage
     * @param string $permitUsage
     *
     * @return string
     */
    private function generateTranslationKey($standardOrCabotage, $permitUsage)
    {
        return sprintf(
            'permits.irhp.range.type.%s.%s',
            self::STANDARD_OR_CABOTAGE_KEY_MAPPINGS[$standardOrCabotage],
            self::PERMIT_USAGE_KEY_MAPPINGS[$permitUsage]
        );
    }
}
