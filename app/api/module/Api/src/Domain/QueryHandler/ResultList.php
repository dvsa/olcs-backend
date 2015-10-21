<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Doctrine\Common\Collections\Collection;

/**
 * Result List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ResultList extends Result
{
    /**
     * @var BundleSerializableInterface[]
     */
    private $objects;

    public function __construct($objects, array $bundle = [], array $values = [])
    {
        if ($objects instanceof Collection) {
            $objects = $objects->toArray();
        }

        $this->objects = $objects;
        $this->bundle = $bundle;
        $this->values = $values;
    }

    /**
     * Recursively serialize objects based on the bundle
     */
    public function serialize()
    {
        $list = [];

        foreach ($this->objects as $object) {
            $data = $object->serialize($this->bundle);
            $data = array_merge($data, $this->values);
            $list[] = $data;
        }

        return $list;
    }
}
