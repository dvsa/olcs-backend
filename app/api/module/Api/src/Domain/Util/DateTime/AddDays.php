<?php

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

use Olcs\Logging\Log\Logger;

/**
 * Add Days
 */
class AddDays implements DateTimeCalculatorInterface
{
    /**
     * Calculates a date that is $days before/after $date.
     *
     * @param \DateTime $date Should be
     * @param integer $days The number of days to offset (can be a negative number)
     * @return \DateTime
     */
    public function calculateDate(\DateTime $date, $days)
    {
        // ensure date is set to midnight to avoid date discrepancies
        $date->setTime(0, 0, 0);

        Logger::debug('AddDays : processing days -> ' . $days);
        $endDate = clone $date;
        if ($days > 0) {
            $endDate->add(new \DateInterval('P' . $days . 'D'));
        } else {
            $endDate->sub(new \DateInterval('P' . abs($days) . 'D'));
        }

        return $endDate;
    }
}
