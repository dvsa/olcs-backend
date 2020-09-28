<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\Availability\StockAvailabilityCounter;
use Dvsa\Olcs\Api\Service\Permits\Availability\StockLicenceMaxPermittedCounter;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class NoOfPermitsGenerator implements ElementGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

    /** @var FeeTypeRepository */
    private $feeTypeRepo;

    /** @var NoOfPermitsFactory */
    private $noOfPermitsFactory;

    /** @var EmissionsCategoryConditionalAdder */
    private $emissionsCategoryConditionalAdder;

    /** @var StockAvailabilityCounter */
    private $stockAvailabilityCounter;

    /** @var StockLicenceMaxPermittedCounter */
    private $stockLicenceMaxPermittedCounter;

    /**
     * Create service instance
     *
     * @param FeeTypeRepository $feeTypeRepo
     * @param NoOfPermitsFactory $noOfPermitsFactory
     * @param EmissionsCategoryConditionalAdder $emissionsCategoryConditionalAdder
     * @param StockAvailabilityCounter $stockAvailabilityCounter
     * @param StockLicenceMaxPermittedCounter $stockLicenceMaxPermittedCounter
     *
     * @return NoOfPermitsGenerator
     */
    public function __construct(
        FeeTypeRepository $feeTypeRepo,
        NoOfPermitsFactory $noOfPermitsFactory,
        EmissionsCategoryConditionalAdder $emissionsCategoryConditionalAdder,
        StockAvailabilityCounter $stockAvailabilityCounter,
        StockLicenceMaxPermittedCounter $stockLicenceMaxPermittedCounter
    ) {
        $this->feeTypeRepo = $feeTypeRepo;
        $this->noOfPermitsFactory = $noOfPermitsFactory;
        $this->emissionsCategoryConditionalAdder = $emissionsCategoryConditionalAdder;
        $this->stockAvailabilityCounter = $stockAvailabilityCounter;
        $this->stockLicenceMaxPermittedCounter = $stockLicenceMaxPermittedCounter;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpApplication = $context->getQaEntity();

        $irhpPermitApplication = $irhpApplication->getFirstIrhpPermitApplication();
        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
        $irhpPermitStockId = $irhpPermitStock->getId();
        $licence = $irhpApplication->getLicence();

        $applicationFee = $this->feeTypeRepo->getLatestByProductReference(
            $irhpApplication->getApplicationFeeProductReference()
        );

        $issueFee = $this->feeTypeRepo->getLatestByProductReference(
            $irhpApplication->getIssueFeeProductReference()
        );

        $maxPermitted = $this->stockLicenceMaxPermittedCounter->getCount($irhpPermitStock, $licence);

        $maxCanApplyFor = $maxPermitted;
        $stockAvailability = $this->stockAvailabilityCounter->getCount($irhpPermitStockId);
        if ($stockAvailability < $maxCanApplyFor) {
            $maxCanApplyFor = $stockAvailability;
        }

        $noOfPermits = $this->noOfPermitsFactory->create(
            $maxCanApplyFor,
            $maxPermitted,
            $applicationFee->getFixedValue(),
            $issueFee->getFixedValue()
        );

        $this->emissionsCategoryConditionalAdder->addIfRequired(
            $noOfPermits,
            'euro5',
            $irhpPermitApplication->getRequiredEuro5(),
            RefData::EMISSIONS_CATEGORY_EURO5_REF,
            $irhpPermitStockId
        );

        $this->emissionsCategoryConditionalAdder->addIfRequired(
            $noOfPermits,
            'euro6',
            $irhpPermitApplication->getRequiredEuro6(),
            RefData::EMISSIONS_CATEGORY_EURO6_REF,
            $irhpPermitStockId
        );

        return $noOfPermits;
    }
}
