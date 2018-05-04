<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Class IsScottishRules
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter
 */
class NoticePeriod extends AbstractFilter
{
    const SCOTTISH_NOTICE_PERIOD = 1;
    const OTHER_NOTICE_PERIOD = 2;
    const WELSH_NOTICE_PERIOD = 3;

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $value['busNoticePeriod'] = self::OTHER_NOTICE_PERIOD;
        foreach ($value['trafficAreas'] as $trafficArea) {
            if ($trafficArea == 'Scottish') {
                $value['busNoticePeriod'] = self::SCOTTISH_NOTICE_PERIOD;
                break;
            }
            if ($trafficArea == 'Welsh') {
                $value['busNoticePeriod'] = self::WELSH_NOTICE_PERIOD;
            }
        }

        return $value;
    }
}
