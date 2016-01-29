<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter\Format;

use Zend\Filter\AbstractFilter as ZendAbstractFilter;

/**
 * Class ExistingRegNo
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter\Format
 */
class ExistingRegNo extends ZendAbstractFilter
{
    /**
     * Returns the result of filtering $value, a regNo we can use to retreive the previous bus reg
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $value['existingRegNo'] = $value['licNo'] . '/' . $value['routeNo'];

        return $value;
    }
}
