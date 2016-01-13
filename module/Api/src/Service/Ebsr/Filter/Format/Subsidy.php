<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter\Format;

use Zend\Filter\Exception;
use Zend\Filter\AbstractFilter as ZendAbstractFilter;

/**
 * Class Subsidy
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter\Format
 */
class Subsidy extends ZendAbstractFilter
{
    protected $map = [
        'none' => 'bs_no',
        'partial' => 'bs_in_part',
        'full' => 'bs_yes'
    ];

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        //if we've no value, default to "none"
        if (!isset($value['subsidised'])) {
            $value['subsidised'] = 'none';
        }

        $value['subsidised'] = $this->map[$value['subsidised']];

        return $value;
    }
}
