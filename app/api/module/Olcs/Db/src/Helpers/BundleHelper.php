<?php

/**
 * Bundle Helper
 *
 * Takes care of the conversion of entites between top-level referenced entities and nested entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Helpers;

use Olcs\Db\Entity\AbstractEntity;
use Olcs\Db\Exceptions\EntityTypeNotFoundException;

/**
 * Bundle Helper
 *
 * Takes care of the conversion of entites between top-level referenced entities and nested entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleHelper
{

    private $entityNamespace = '\Olcs\Db\Entity\\';
    private $entities = array();

    /**
     * Convert an array of top level entites into a nested entity
     *
     * @param array $entities
     * @return object
     */
    public function getNestedEntityFromEntities(array $entityArray)
    {
        $this->entities = $this->createEntitiesFromArray($entityArray);

        foreach ($entityArray as $reference => $entityProperties) {

            $this->setEntityProperties($this->entities[$reference], $entityProperties);
        }

        return $this->entities[array_keys($entityArray)[0]];
    }

    /**
     * Convert a nested entity to top level entities
     *
     * @param AbstractEntity $object
     * @return array
     */
    public function getTopLevelEntitiesFromNestedEntity(AbstractEntity $object)
    {
        print_r($object);
        return array();
    }

    /**
     * Create entity objects from array
     *
     * @param array $array
     * @return array
     */
    private function createEntitiesFromArray($array)
    {
        $entities = array();

        foreach (array_keys($array) as $reference) {

            $entityClassName = $this->getEntityClassNameFromReference($reference);
            $entities[$reference] = new $entityClassName();

            if (!$entities[$reference] instanceof AbstractEntity) {
                throw new EntityTypeNotFoundException($entityClassName);
            }
        }

        return $entities;
    }

    /**
     * Set the properties on the entity
     *
     * @param AbstractEntity $entity
     * @param array $properties
     */
    private function setEntityProperties(AbstractEntity $entity, array $properties)
    {
        foreach ($properties as $propertyName => $propertyValue) {

            if ($propertyName !== '__REFS') {

                $this->setProperty($entity, $propertyName, $propertyValue);
            } else {

                $this->setReference($entity, $propertyValue);
            }
        }
    }

    /**
     * Set property value
     *
     * @param AbstractEntity $entity
     * @param string $propertyName
     * @param mixed $propertyValue
     */
    private function setProperty(AbstractEntity $entity, $propertyName, $propertyValue)
    {
        $propertySetter = $this->getSetterMethodNameForProperty($propertyName);

        if (method_exists($entity, $propertySetter)) {

            $entity->$propertySetter($propertyValue);
        }
    }

    /**
     * Set reference
     *
     * @param AbstractEntity $entity
     * @param array $references
     */
    private function setReference(AbstractEntity $entity, $references)
    {
        foreach ($references as $entityPropertyName => $entityPropertyReferences) {

            if (is_array($entityPropertyReferences)) {

                $this->addMultipleReferences($entity, $entityPropertyName, $entityPropertyReferences);
            } else {

                $methodName = $this->getSetterMethodNameForProperty($entityPropertyName);

                $entity->$methodName($this->entities[$entityPropertyReferences]);
            }
        }
    }

    /**
     * Add multiple references
     *
     * @param AbstractEntity $entity
     * @param string $name
     * @param array $references
     */
    private function addMultipleReferences(AbstractEntity $entity, $name, array $references)
    {
        $methodName = $this->getAddMethodNameForProperty($name);

        foreach ($references as $reference) {
            $entity->$methodName($this->entities[$reference]);
        }
    }

    /**
     * Get the entity class name from the reference
     *
     * @param string $reference
     * @return string
     */
    private function getEntityClassNameFromReference($reference)
    {
        $entityName = strtok($reference, '/');

        return $this->entityNamespace . $entityName;
    }

    /**
     * Get the setter name for the property
     *
     * @param string $property
     * @return string
     */
    private function getSetterMethodNameForProperty($property)
    {
        return 'set' . ucwords($property);
    }

    /**
     * Get add method name for property
     *
     * @param string $property
     * @return string
     */
    private function getAddMethodNameForProperty($property)
    {
        return 'add' . substr(ucwords($property), 0, -1);
    }

}
