<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Class InjectIsTxcApp
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter
 */
class InjectIsTxcApp extends AbstractFilter
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
        $value['isTxcApp'] = 'Y';
        $value['ebsrRefresh'] = 'N';

        if (isset($value['txcAppType']) && $value['txcAppType'] == 'nonChargeableChange') {
            $value['ebsrRefresh'] = 'Y';
        }

        return $value;
    }
}
