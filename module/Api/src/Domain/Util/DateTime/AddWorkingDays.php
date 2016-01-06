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

        $workingDate = new \DateTime();
        $workingDate->setTimestamp(strtotime("$days weekdays", $date->getTimestamp()));
        $totalDays = $date->diff($workingDate)->format('%a');

        Logger::debug('AddWorkingDays : processing working days -> ' . $totalDays);

        return $this->wrapped->calculateDate($date, $totalDays);
    }
}
