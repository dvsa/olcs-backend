<?php

namespace Dvsa\Olcs\Api\Service\Publication;

/**
 * Class ImmutableArrayObject
 * @package Dvsa\Olcs\Api\Service\Publication
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
