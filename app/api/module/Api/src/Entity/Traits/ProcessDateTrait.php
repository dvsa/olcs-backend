<?php

namespace Dvsa\Olcs\Api\Entity\Traits;

/**
 * ProcessDate Trait
 */
trait ProcessDateTrait
{
    /**
     * Processes date
     *
     * @param string $date     Date
     * @param string $format   Format
     * @param bool   $zeroTime Zero time
     *
     * @return \DateTime|null
     */
    public function processDate($date, $format = 'Y-m-d', $zeroTime = true)
    {
        $dateTime = \DateTime::createFromFormat($format, $date);

        if (!$dateTime instanceof \DateTime) {
            return null;
        }

        if ($zeroTime) {
            $dateTime->setTime(0, 0, 0);
        }

        return $dateTime;
    }
}
