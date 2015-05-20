<?php
namespace Dvsa\Olcs\Api\Domain\Util;

use Dvsa\Olcs\Api\Entity\System\Sla;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Util\DateTime\DateTimeCalculatorInterface;


/**
 * Interface TimeProcessorBuilderInterface
 * @package Dvsa\Olcs\Api\Domain\Util
 */
interface TimeProcessorBuilderInterface
{
    /**
     * @param Sla $sla
     * @param TrafficArea $trafficArea
     * @return DateTimeCalculatorInterface
     */
    public function build(Sla $sla, TrafficArea $trafficArea);
}
