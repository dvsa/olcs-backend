<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use Dvsa\Olcs\Api\Service\Qa\Common\CurrentDateTimeFactory;
use Dvsa\Olcs\Api\Service\Qa\Common\DateIntervalFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\DateGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;

class PermitStartDateGenerator implements ElementGeneratorInterface
{
    const DATE_INTERVAL = 'P60D';

    /** @var PermitStartDateFactory */
    private $permitStartDateFactory;

    /** @var CurrentDateTimeFactory */
    private $currentDateTimeFactory;

    /** @var DateIntervalFactory */
    private $dateIntervalFactory;

    /** @var DateGenerator */
    private $dateGenerator;

    /**
     * Create service instance
     *
     * @param PermitStartDateFactory $permitStartDateFactory
     * @param CurrentDateTimeFactory $currentDateTimeFactory
     * @param DateIntervalFactory $dateIntervalFactory
     * @param DateGenerator $dateGenerator
     *
     * @return PermitStartDateGenerator
     */
    public function __construct(
        PermitStartDateFactory $permitStartDateFactory,
        CurrentDateTimeFactory $currentDateTimeFactory,
        DateIntervalFactory $dateIntervalFactory,
        DateGenerator $dateGenerator
    ) {
        $this->permitStartDateFactory = $permitStartDateFactory;
        $this->currentDateTimeFactory = $currentDateTimeFactory;
        $this->dateIntervalFactory = $dateIntervalFactory;
        $this->dateGenerator = $dateGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $dateMustBeBefore = $this->currentDateTimeFactory->create();
        
        $dateMustBeBefore->add(
            $this->dateIntervalFactory->create(self::DATE_INTERVAL)
        );

        return $this->permitStartDateFactory->create(
            $dateMustBeBefore,
            $this->dateGenerator->generate($context)
        );
    }
}
