<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter\Format;

use Laminas\Filter\AbstractFilter as LaminasAbstractFilter;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class MemberStateCode extends LaminasAbstractFilter
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
