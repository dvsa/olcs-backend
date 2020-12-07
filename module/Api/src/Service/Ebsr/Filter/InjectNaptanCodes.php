<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter;

use Laminas\Filter\AbstractFilter;
use Laminas\Filter\Exception;

/**
 * Class InjectNaptanCodes
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter
 */
class InjectNaptanCodes extends AbstractFilter
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
        $naptan = [];

        foreach ($value['stops'] as $stop) {
            $extractedCode = substr($stop, 0, 3);
            $naptan[$extractedCode] = $extractedCode;
        }

        $value['naptan'] = $naptan;

        return $value;
    }
}
