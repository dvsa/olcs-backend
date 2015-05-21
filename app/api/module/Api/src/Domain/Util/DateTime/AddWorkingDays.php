<?php

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

/**
 * Class AddWorkingDays
 */
class AddWorkingDays implements DateTimeCalculatorInterface
{
    private $wrapped;

    public function __construct(DateTimeCalculatorInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * Calculates a date that is $days before/after $date. Optionally takes into account weekends and holidays.
     *
     * @param \DateTime $date Should be
     * @param integer $days The number of days to offset (can be a negative number)
     * @param boolean $we Should weekend days be considered/excluded
     * @param boolean $bh Should public holidays be considered/excluded
     * @return \DateTime
     */
    public function calculateDate(\DateTime $date, $days)
    {
        $currentDay = (int) $date->format('w');
        $workingWeeks = floor($days / 5);
        $totalDays = $workingWeeks * 7;
        $daysLeft = $days - ($workingWeeks * 5);

        $t = $currentDay + $daysLeft;

        if ($t % 5 !== $t % 7) {
            $daysLeft +=2;
        }

        $totalDays += $daysLeft;

        return $this->wrapped->calculateDate($date, $totalDays);
    }
}
