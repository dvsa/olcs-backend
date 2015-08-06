<?php

namespace Dvsa\Olcs\Api\Service\Submission;

/**
 * Class ImmutableArrayObject
 * @package Dvsa\Olcs\Api\Service\Submission
 */
class ImmutableArrayObject extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
    }

    public function offsetUnset($index)
    {
    }

    public function exchangeArray($input)
    {
    }
}
