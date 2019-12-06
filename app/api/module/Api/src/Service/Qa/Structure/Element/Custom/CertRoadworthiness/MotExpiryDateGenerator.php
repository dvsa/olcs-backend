<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\CertRoadworthiness;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common\DateWithThresholdGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;

class MotExpiryDateGenerator implements ElementGeneratorInterface
{
    const DATE_THRESHOLD = 'P13M';

    /** @var DateWithThresholdGenerator */
    private $dateWithThresholdGenerator;

    /**
     * Create service instance
     *
     * @param DateWithThresholdGenerator $dateWithThresholdGenerator
     *
     * @return MotExpiryDateGenerator
     */
    public function __construct(DateWithThresholdGenerator $dateWithThresholdGenerator)
    {
        $this->dateWithThresholdGenerator = $dateWithThresholdGenerator;
    }

    /**
     * Build and return an element instance using the appropriate data sources
     *
     * @param ElementGeneratorContext $context
     *
     * @return ElementInterface
     */
    public function generate(ElementGeneratorContext $context)
    {
        return $this->dateWithThresholdGenerator->generate($context, self::DATE_THRESHOLD);
    }
}