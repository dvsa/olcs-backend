<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler;

/**
 * Result
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Result
{
    /**
     * @var BundleSerializableInterface
     */
    protected $object;

    /**
     * @var array
     */
    protected $bundle;

    /**
     * @var array
     */
    protected $values = [];

    public function __construct(BundleSerializableInterface $object, array $bundle = [], array $values = [])
    {
        $this->object = $object;
        $this->bundle = $bundle;
        $this->values = $values;
    }

    /**
     * Recursively serialize objects based on the bundle
     */
    public function serialize()
    {
        $data = $this->object->serialize($this->bundle);

        return array_merge($data, $this->values);
    }
}
