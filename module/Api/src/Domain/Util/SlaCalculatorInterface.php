<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Dvsa\Olcs\Api\Entity\System\Sla;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

/**
 * Interface SlaCalculatorInterface
 * @package Dvsa\Olcs\Api\Domain\Util
 */
interface SlaCalculatorInterface
{
    /**
     * @param \DateTime $date
     * @param Sla $sla
     * @param TrafficArea $trafficArea
     * @return \DateTime
     */
    public function applySla(\DateTime $date, Sla $sla, TrafficArea $trafficArea);
}
