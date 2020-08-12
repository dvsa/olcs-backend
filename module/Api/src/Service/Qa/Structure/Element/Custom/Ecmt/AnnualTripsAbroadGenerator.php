<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\TextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class AnnualTripsAbroadGenerator implements ElementGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

    /** @var AnnualTripsAbroadFactory */
    private $annualTripsAbroadFactory;

    /** @var TextGenerator */
    private $textGenerator;

    /**
     * Create service instance
     *
     * @param AnnualTripsAbroadFactory $annualTripsAbroadFactory
     * @param TextGenerator $textGenerator
     *
     * @return AnnualTripsAbroadGenerator
     */
    public function __construct(AnnualTripsAbroadFactory $annualTripsAbroadFactory, TextGenerator $textGenerator)
    {
        $this->annualTripsAbroadFactory = $annualTripsAbroadFactory;
        $this->textGenerator = $textGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpApplication = $context->getQaEntity();

        return $this->annualTripsAbroadFactory->create(
            $irhpApplication->getIntensityOfUseWarningThreshold(),
            $irhpApplication->getLicence()->isNi(),
            $this->textGenerator->generate($context)
        );
    }
}
