<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter\Format;

use Zend\Filter\AbstractFilter as ZendAbstractFilter;

/**
 * Class Via
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter\Format
 */
class Via extends ZendAbstractFilter
{

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        if (isset($value['via']) && is_array($value['via'])) {
            $value['via'] = implode(', ', array_unique($value['via']));
        }

        return $value;
    }
}
