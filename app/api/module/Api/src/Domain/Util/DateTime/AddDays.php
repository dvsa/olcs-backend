<?php

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

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
        $endDate = clone $date;
        if ($days > 0) {
            $endDate->add(new \DateInterval('P' . $days . 'D'));
        } else {
            $endDate->sub(new \DateInterval('P' . abs($days) . 'D'));
        }

        return $endDate;
    }
}
