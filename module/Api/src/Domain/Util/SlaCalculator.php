<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\Sla;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Olcs\Logging\Log\Logger;

/**
 * Class SlaCalculator
 * @package Dvsa\Olcs\Api\Domain\Util
 */
final class SlaCalculator implements SlaCalculatorInterface
{
    /**
     * @var TimeProcessorBuilderInterface
     */
    private $timeProcessorBuilder;

    /**
     * @param TimeProcessorBuilderInterface $timeProcessorBuilder
     */
    public function __construct(TimeProcessorBuilderInterface $timeProcessorBuilder)
    {
        $this->timeProcessorBuilder = $timeProcessorBuilder;
    }

    /**
     * @param \DateTime $date the starting reference date (the SLA compareTo)
     * @param Sla $sla SLA to process
     * @param $trafficArea
     * @return \DateTime
     */
    public function applySla(\DateTime $date, Sla $sla, TrafficArea $trafficArea)
    {
        Logger::debug(
            'BEGIN SLA for ' . $sla->getField() . ' ' . $sla->getDays() . ' days from ' .
            $sla->getCompareTo()
        );
        // generate the appropriate object to do the calculation determined by the sla and whether to take weekends
        // and/or public holidays into account
        $dateTimeProcessor = $this->timeProcessorBuilder->build($sla, $trafficArea);

        $targetDate = $dateTimeProcessor->calculateDate($date, $sla->getDays());
        Logger::debug("FINAL TARGET DATE => " . $targetDate->format('d/m/Y'));
        Logger::debug('END SLA' . "\n");

        return $targetDate;
    }
}
