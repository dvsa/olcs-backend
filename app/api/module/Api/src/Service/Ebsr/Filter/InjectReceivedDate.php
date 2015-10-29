<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Class InjectReceivedDate
 * @package Olcs\Ebsr\Filter
 */
class InjectReceivedDate extends AbstractFilter
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
        $value['receivedDate'] = date('Y-m-d');
        return $value;
    }
}
