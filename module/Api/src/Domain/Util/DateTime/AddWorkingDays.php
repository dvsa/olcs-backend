<?php

namespace Dvsa\Olcs\Api\Domain\Util\DateTime;

use Olcs\Logging\Log\Logger;

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
        // ensure date is set to midnight to avoid date discrepancies
        $date->setTime(0,0,0);

        $workingDate = new \DateTime();
        Logger::debug('DIFF date -> ' . $date->format('r'));

        $workingDate->setTimestamp(strtotime("$days weekdays", $date->getTimestamp()));
        Logger::debug('workingDate -> ' . $workingDate->format('r'));

        $totalDays = $date->diff($workingDate)->format('%r%a');

        Logger::debug('AddWorkingDays : processing working days -> ' . $totalDays);

        return $this->wrapped->calculateDate($date, $totalDays);
    }
}
