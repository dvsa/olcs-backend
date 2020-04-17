<?php

namespace Dvsa\Olcs\Api\Service\Permits\FeeBreakdown;

use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
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
                $applicationFeeType = $this->getFeeType(
                    $irhpPermitApplication,
                    $standardOrCabotage,
                    IrhpPermitApplication::BILATERAL_APPLICATION_FEE_KEY
                );

                $issueFeeType = $this->getFeeType(
                    $irhpPermitApplication,
                    $standardOrCabotage,
                    IrhpPermitApplication::BILATERAL_ISSUE_FEE_KEY
                );

                $feePerPermit = $irhpPermitApplication->getBilateralFeePerPermit($applicationFeeType, $issueFeeType);
                $total = $feePerPermit * $quantity;

                $rows[] = [
                    'countryName' => $countryName,
                    'type' => $this->generateTranslationKey($standardOrCabotage, $permitUsage),
                    'quantity' => $quantity,
                    'total' => $total,
                ];
            }
        }

        return $rows;
    }

    /**
     * Get the fee type associated with the specified parameters
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param string $standardOrCabotage
     * @param string $feeTypeKey
     *
     * @return FeeType
     */
    private function getFeeType(IrhpPermitApplication $irhpPermitApplication, $standardOrCabotage, $feeTypeKey)
    {
        $productReference = $irhpPermitApplication->getBilateralFeeProductReference($standardOrCabotage, $feeTypeKey);

        return $this->feeTypeRepo->getLatestByProductReference($productReference);
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
            'permits.irhp.fee-breakdown.type.%s.%s',
            self::STANDARD_OR_CABOTAGE_KEY_MAPPINGS[$standardOrCabotage],
            self::PERMIT_USAGE_KEY_MAPPINGS[$permitUsage]
        );
    }
}
