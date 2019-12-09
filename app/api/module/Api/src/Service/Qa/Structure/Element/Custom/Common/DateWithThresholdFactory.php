<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common;

use DateTime;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date as DateElement;

class DateWithThresholdFactory
{
    /**
     * Create and return a DateWithThreshold instance
     *
     * @param DateTime $dateThreshold
     * @param DateElement $date
     *
     * @return DateWithThreshold
     */
    public function create(DateTime $dateThreshold, DateElement $date)
    {
        return new DateWithThreshold($dateThreshold, $date);
    }
}
