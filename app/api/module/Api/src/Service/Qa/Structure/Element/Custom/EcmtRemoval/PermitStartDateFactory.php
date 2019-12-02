<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use DateTime;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date as DateElement;

class PermitStartDateFactory
{
    /**
     * Create and return a PermitStartDate instance
     *
     * @param DateTime $dateMustBeBefore
     * @param DateElement $date
     *
     * @return PermitStartDate
     */
    public function create(DateTime $dateMustBeBefore, DateElement $date)
    {
        return new PermitStartDate($dateMustBeBefore, $date);
    }
}
