<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter\Format;

use Zend\Filter\AbstractFilter as ZendAbstractFilter;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class MemberStateCode extends ZendAbstractFilter
{
    /**
     * Change 'UK' to 'GB'
     *
     * @param array $value input value
     *
     * @return array
     */
    public function filter($value)
    {
        if (strtoupper($value['memberStateCode']) === 'UK') {
            $value['memberStateCode'] = 'GB';
        }

        return $value;
    }
}
