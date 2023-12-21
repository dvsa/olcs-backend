<?php

namespace Dvsa\Olcs\Api\Service\Publication;

/**
 * @template-extends \ArrayObject<int, mixed>
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
        return [];
    }
}
