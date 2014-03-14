<?php

/**
 * Bundle Hydrator
 *
 * Takes care of the conversion of entites between top-level referenced entities and nested entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Utility;

use OlcsEntities\Entity\AbstractEntity;
use Olcs\Db\Exceptions\EntityTypeNotFoundException;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Doctrine\Common\Collections\Collection;

/**
 * Bundle Hydrator
 *
 * Takes care of the conversion of entites between top-level referenced entities and nested entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleHydrator
{
    private $entityNamespace = '\OlcsEntities\Entity\\';
    private $entities = array();
    private $hydrator;
    private $objectReferences = array();

    /**
     * Inject hydrator dependency
     *
     * @param AbstractHydrator $hydrator
     */
    public function __construct(AbstractHydrator $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Configure the entity namespace
     *  - useful for unit testing
     * @param string $namespace
     */
    public function setEntityNamespace($namespace)
    {
        $this->entityNamespace = $namespace;
    }

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

        return $this->entities;
    }

    /**
     * Convert a nested entity to top level entities
     *
     * @param mixed $input
     * @param boolean $recursive
     * @param string $mainReference
     * @return array
     */
    public function getTopLevelEntitiesFromNestedEntity($input, $recursive = true, $mainReference = null)
    {
        if ($input instanceof AbstractEntity) {

            return $this->getTopLevelEntitiesFromObject($input, $recursive, $mainReference);

        } elseif (is_array($input)) {

            foreach ($input as $entity) {

                $this->getTopLevelEntitiesFromObject($entity, $recursive, $mainReference);
            }

            return $this->entities;
        }

        // TODO: Replace this with an alternative Exception class
        throw new \Exception('Expected an Entity or an array of Entities');
    }

    /**
     * Convert an entity to top level entities
     *
     * @param AbstractEntity $object
     * @param boolean $recursive
     * @param string $mainReference
     * @return array
     */
    private function getTopLevelEntitiesFromObject(AbstractEntity $object, $recursive = true, $mainReference = null)
    {
        if (is_null($mainReference)) {
            $mainReference = $this->getReference($this->getEntityName($object));
        }

        $this->entities[$mainReference] = $this->hydrator->extract($object);

        foreach ($this->entities[$mainReference] as $name => $value) {

            if (is_object($value)) {

                unset($this->entities[$mainReference][$name]);

                if ($value instanceof Collection && $recursive) {

                    $references = array();

                    foreach ($value as $entity) {

                        $references[] = $this->getReferenceForEntity($entity);
                    }

                } elseif ($value instanceof AbstractEntity) {

                    $references = $this->getReferenceForEntity($value);
                }

                if (!isset($this->entities[$mainReference]['__REFS'])) {

                    $this->entities[$mainReference]['__REFS'] = array();
                }

                $this->entities[$mainReference]['__REFS'][$name] = $references;
            }
        }

        return $this->entities;
    }

    /**
     * Find a reference for an entity
     *
     * @param object $entity
     * @return string
     */
    private function getReferenceForEntity($entity)
    {
        if (false !== ($key = array_search($entity, $this->objectReferences))) {

            $reference = $key;

        } else {

            $reference = $this->getReference($this->getEntityName($entity));
            $this->getTopLevelEntitiesFromNestedEntity($entity, false, $reference);
            $this->objectReferences[$reference] = $entity;
        }

        return $reference;
    }

    /**
     * Get the next reference for an entity
     *
     * @param string $entityName
     * @return string
     */
    private function getReference($entityName)
    {
        $id = -1;

        foreach (array_keys($this->entities) as $reference) {

            if (preg_match('/^' . $entityName . '\/([0-9]+)$/', $reference, $matches)) {

                $id = max($id, (int) $matches[1]);
            }
        }

        $id++;

        return $entityName . '/' . $id;
    }

    /**
     * Get the entity name
     *
     * @param object $entity
     * @return string
     */
    private function getEntityName($entity)
    {
        $className = get_class($entity);
        $parts = explode('\\', (string) $className);
        return array_pop($parts);
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

            if (class_exists($entityClassName)) {

                $entities[$reference] = new $entityClassName();

            } else {

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
