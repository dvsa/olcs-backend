<?php

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

/**
 * AddDaysExcludingDates
 */
class AddDaysExcludingDates implements DateTimeCalculatorInterface
{
    private $wrapped;
    private $excluded;

    public function __construct(DateTimeCalculatorInterface $wrapped, DateProviderInterface $excluded)
    {
        $this->wrapped = $wrapped;
        $this->excluded = $excluded;
    }

    /**
     * Calculates a date that is $days before/after $date. Takes into account weekends and holidays.
     *
     * @param \DateTime $date Should be
     * @param integer $days The number of days to offset (can be a negative number)
     * @return \DateTime
     */
    public function calculateDate(\DateTime $date, $days)
    {
        $endDate = $this->wrapped->calculateDate($date, $days);

        $excludedDates = $this->excluded->between($date, $endDate);

        return $this->wrapped->calculateDate($endDate, count($excludedDates));
    }
}
