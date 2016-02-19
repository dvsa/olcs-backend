<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter\Format;

use Zend\Filter\AbstractFilter as ZendAbstractFilter;

/**
 * Class SiDates
 * @package Dvsa\Olcs\Api\Service\Nr\Filter\Format
 */
class SiDates extends ZendAbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  array $value
     * @return array
     */
    public function filter($value)
    {
        foreach ($value['imposedErrus'] as $key => $dates) {
            if (isset($dates['startDate'])) {
                $value['imposedErrus'][$key]['startDate'] = $this->createDate($dates['startDate']);
            } else {
                $value['imposedErrus'][$key]['startDate'] = null;
            }

            if (isset($dates['endDate'])) {
                $value['imposedErrus'][$key]['endDate'] = $this->createDate($dates['endDate']);
            } else {
                $value['imposedErrus'][$key]['endDate'] = null;
            }

            $value['imposedErrus'][$key]['finalDecisionDate'] = $this->createDate($dates['finalDecisionDate']);
        }

        $value['checkDate'] = $this->createDate($value['checkDate']);
        $value['infringementDate'] = $this->createDate($value['infringementDate']);

        return $value;
    }

    /**
     * creates a date and resets the time fields to zero
     *
     * @param string $date
     * @return \DateTime|null
     */
    private function createDate($date)
    {
        $dateTime = new \DateTime($date);
        $dateTime->setTime(0, 0, 0);

        return $dateTime;
    }
}
