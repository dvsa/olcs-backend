<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Class IsScottishRules
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter
 */
class IsScottishRules extends AbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $value['busNoticePeriod'] = 2;

        foreach ($value['trafficAreas'] as $trafficArea) {
            if ($trafficArea == 'Scottish') {
                $value['busNoticePeriod'] = 1;
            }
        }

        return $value;
    }
}
