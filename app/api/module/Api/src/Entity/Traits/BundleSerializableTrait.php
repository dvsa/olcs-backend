<?php

namespace Dvsa\Olcs\Api\Entity\Traits;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;
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
    public function serialize(array $bundle = [])
    {
        $output = [];

        $excludeProperties = [
            '__initializer__',
            '__cloner__',
            '__isInitialized__',
        ];

        $vars = get_object_vars($this);

        foreach ($vars as $property => $value) {

            if (in_array($property, $excludeProperties)) {
                continue;
            }

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
                return $value->serialize();
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

            // Allow criteria mid-bundle
            if (isset($propertyBundle['criteria'])) {
                $value = $value->matching($propertyBundle['criteria']);
            }

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
            try {
                // try to serialize the value
                return $value->serialize($propertyBundle);
            } catch (EntityNotFoundException $ex) {
                // we may have the object id but will not be able to load it
                // because SoftDeleteable is used
                return null;
            }
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

    /**
     * This method allows our entities to be cast to a string, so we can use "in" criteria with just id's
     * when a collection is initialized
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->getId();
    }
}
