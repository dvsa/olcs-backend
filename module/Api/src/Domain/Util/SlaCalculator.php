<?php

namespace Dvsa\Olcs\Api\Domain\Util;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\Sla;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

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
     * @param \DateTime $date
     * @param Sla $sla
     * @param $trafficArea
     * @return \DateTime
     */
    public function applySla(\DateTime $date, Sla $sla, TrafficArea $trafficArea)
    {
        $dateTimeProcessor = $this->timeProcessorBuilder->build($sla, $trafficArea);

        return $dateTimeProcessor->calculateDate($date, $sla->getDays());
    }
}