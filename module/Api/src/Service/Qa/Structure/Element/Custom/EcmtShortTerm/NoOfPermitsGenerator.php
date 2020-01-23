<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use RuntimeException;

class NoOfPermitsGenerator implements ElementGeneratorInterface
{
    const TOT_AUTH_VEHICLES_MULTIPLIER = 2;

    /** @var NoOfPermitsFactory */
    private $noOfPermitsFactory;

    /** @var EmissionsCategoryConditionalAdder */
    private $emissionsCategoryConditionalAdder;

    /**
     * Create service instance
     *
     * @param NoOfPermitsFactory $noOfPermitsFactory
     * @param EmissionsCategoryConditionalAdder $emissionsCategoryConditionalAdder
     *
     * @return NoOfPermitsGenerator
     */
    public function __construct(
        NoOfPermitsFactory $noOfPermitsFactory,
        EmissionsCategoryConditionalAdder $emissionsCategoryConditionalAdder
    ) {
        $this->noOfPermitsFactory = $noOfPermitsFactory;
        $this->emissionsCategoryConditionalAdder = $emissionsCategoryConditionalAdder;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpApplication = $context->getIrhpApplicationEntity();

        $totAuthVehiclesMultiplierMappings = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT => 1,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM => 2,
        ];

        $irhpPermitTypeId = $irhpApplication->getIrhpPermitType()->getId();
        if (!array_key_exists($irhpPermitTypeId, $totAuthVehiclesMultiplierMappings)) {
            throw new RuntimeException('This question does not support permit type ' . $irhpPermitTypeId);
        }

        $irhpPermitApplication = $irhpApplication->getFirstIrhpPermitApplication();
        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
        $irhpPermitStockId = $irhpPermitStock->getId();

        $totAuthVehiclesMultiplier = $totAuthVehiclesMultiplierMappings[$irhpPermitTypeId];

        $noOfPermits = $this->noOfPermitsFactory->create(
            $irhpPermitStock->getValidityYear(),
            $irhpApplication->getLicence()->getTotAuthVehicles() * $totAuthVehiclesMultiplier
        );

        $this->emissionsCategoryConditionalAdder->addIfRequired(
            $noOfPermits,
            FieldNames::REQUIRED_EURO5,
            'qanda.ecmt-short-term.number-of-permits.label.euro5',
            $irhpPermitApplication->getRequiredEuro5(),
            RefData::EMISSIONS_CATEGORY_EURO5_REF,
            $irhpPermitStockId
        );

        $this->emissionsCategoryConditionalAdder->addIfRequired(
            $noOfPermits,
            FieldNames::REQUIRED_EURO6,
            'qanda.ecmt-short-term.number-of-permits.label.euro6',
            $irhpPermitApplication->getRequiredEuro6(),
            RefData::EMISSIONS_CATEGORY_EURO6_REF,
            $irhpPermitStockId
        );

        return $noOfPermits;
    }
}
