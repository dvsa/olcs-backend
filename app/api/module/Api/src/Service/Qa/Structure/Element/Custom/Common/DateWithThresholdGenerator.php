<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common;

use Dvsa\Olcs\Api\Service\Qa\Common\CurrentDateTimeFactory;
use Dvsa\Olcs\Api\Service\Qa\Common\DateIntervalFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\DateGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;

class DateWithThresholdGenerator
{
    /** @var DateWithThresholdFactory */
    private $dateWithThresholdFactory;

    /** @var CurrentDateTimeFactory */
    private $currentDateTimeFactory;

    /** @var DateIntervalFactory */
    private $dateIntervalFactory;

    /** @var DateGenerator */
    private $dateGenerator;

    /**
     * Create service instance
     *
     * @param DateWithThresholdFactory $dateWithThresholdFactory
     * @param CurrentDateTimeFactory $currentDateTimeFactory
     * @param DateIntervalFactory $dateIntervalFactory
     * @param DateGenerator $dateGenerator
     *
     * @return DateWithThresholdGenerator
     */
    public function __construct(
        DateWithThresholdFactory $dateWithThresholdFactory,
        CurrentDateTimeFactory $currentDateTimeFactory,
        DateIntervalFactory $dateIntervalFactory,
        DateGenerator $dateGenerator
    ) {
        $this->dateWithThresholdFactory = $dateWithThresholdFactory;
        $this->currentDateTimeFactory = $currentDateTimeFactory;
        $this->dateIntervalFactory = $dateIntervalFactory;
        $this->dateGenerator = $dateGenerator;
    }

    /**
     * Build and return an element instance using the appropriate data sources
     *
     * @param ElementGeneratorContext $context
     * @param string $dateInterval
     *
     * @return ElementInterface
     */
    public function generate(ElementGeneratorContext $context, $dateInterval)
    {
        $dateThreshold = $this->currentDateTimeFactory->create();
        
        $dateThreshold->add(
            $this->dateIntervalFactory->create($dateInterval)
        );

        return $this->dateWithThresholdFactory->create(
            $dateThreshold,
            $this->dateGenerator->generate($context)
        );
    }
}
