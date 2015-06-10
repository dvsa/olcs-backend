<?php

namespace Dvsa\Olcs\Api\Entity\Traits;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Proxy\Proxy;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Bundle Serializable Trait
 */
trait BundleSerializableTrait
{
    /**
     * @return array
     * @deprecated
     */
    public function jsonSerialize()
    {
        $output = [];

        $vars = get_object_vars($this);

        foreach ($vars as $property => $value) {

            $output[$property] = null;

            if ($value instanceof Proxy) {
                if ($value->__isInitialized()) {
                    $output[$property] = $value;
                }
                continue;
            }

            if ($value instanceof ArrayCollection) {
                $output[$property] = $value->toArray();
                continue;
            }

            if ($value instanceof AbstractLazyCollection) {
                if ($value->isInitialized()) {
                    $output[$property] = $value->toArray();
                }
                continue;
            }

            $output[$property] = $value;
        }

        return array_merge($output, $this->getCalculatedValues());
    }

    /**
     * @return array
     * @deprecated
     */
    protected function getCalculatedValues()
    {
        return [];
    }

    /**
     * @return array
     */
    public function serialize(array $bundle = null)
    {
        $output = [];

        $vars = get_object_vars($this);

        foreach ($vars as $property => $value) {

            if ($value instanceof Proxy
                || $value instanceof ArrayCollection
                || $value instanceof AbstractLazyCollection
                || $value instanceof BundleSerializableInterface
            ) {
                $propertyBundle = null;

                if (in_array($property, $bundle)) {
                    $propertyBundle = [];
                } elseif (array_key_exists($property, $bundle)) {
                    $propertyBundle = $bundle[$property];
                }

                $value = $this->determineValue($value, $property, $propertyBundle);
            }

            $output[$property] = $value;
        }

        return array_merge($output, $this->getCalculatedBundleValues());
    }

    /**
     * Property bundle is null when we haven't asked for the property
     *
     * @param mixed $value
     * @param string $property
     * @param array $propertyBundle
     * @return array|null
     */
    private function determineValue($value, $property, $propertyBundle = null)
    {
        // If we haven't asked for the property
        if ($propertyBundle === null) {
            // ...and it is a RefData entity
            if ($value instanceof RefData
                // ...or initialized proxy
                && (!($value instanceof Proxy) || $value->__isInitialized())
            ) {
                // ...include it anyway
                return $value;
            }

            // ...otherwise bail
            return null;
        }

        // If it's a proxy
        if ($value instanceof Proxy) {
            // ...and not initialized
            if (!$value->__isInitialized()) {
                // ... then initialize it
                $value = $this->getPropertyValue($property);
            }

            // ...then include it
            return $this->getSerializedValue($value, $propertyBundle);
        }

        // If we have an actual entity object
        if ($value instanceof BundleSerializableInterface) {
            // ...then return the serialized entity
            return $value->serialize($propertyBundle);
        }

        // If we have a collection
        if ($value instanceof Collection) {
            $list = [];

            // .. serialize each item and add it to the list
            foreach ($value->toArray() as $item) {
                $list[] = $this->getSerializedValue($item, $propertyBundle);
            }

            return $list;
        }

        return null;
    }

    private function getSerializedValue($value, $propertyBundle)
    {
        if ($value instanceof BundleSerializableInterface) {
            return $value->serialize($propertyBundle);
        }

        return $value;
    }

    private function getPropertyValue($property)
    {
        $value = null;

        $getter = 'get' . ucfirst($property);

        if (method_exists($this, $getter)) {
            $value = $this->$getter();
        }

        return $value;
    }

    /**
     * @return array
     */
    protected function getCalculatedBundleValues()
    {
        return [];
    }
}
