<?php

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

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
        $endDate = $this->wrapped->calculateDate($date, $days);

        $excludedDates = $this->excluded->between($date, $endDate);

        return $this->wrapped->calculateDate($endDate, count($excludedDates));
    }
}