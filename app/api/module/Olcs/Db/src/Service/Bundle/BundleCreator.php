<?php

/**
 * BundleCreator class, handles the bundling of entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service\Bundle;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olcs\Db\Entity\Interfaces\EntityInterface;

/**
 * BundleCreator class, handles the bundling of entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleCreator
{

    /**
     * Holds the hydrator
     *
     * @var array
     */
    private $hydrator;

    /**
     * Inject hydrator
     *
     * @param type $hydrator
     */
    public function __construct($hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Get Hydrator
     *
     * @return type
     */
    private function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * Build an entity bundle, this is to reduce rest calls and reduce payload
     *
     * @param mixed $entity
     * @param array $data
     * @return mixed
     */
    public function buildEntityBundle($entity, $data)
    {
        $bundleConfig = null;

        if (isset($data['bundle'])) {

            $bundleConfig = json_decode($data['bundle'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {

                throw new \Exception('Invalid bundle configuration: Expected JSON');
            }
        }

        if (empty($bundleConfig)) {

            $bundleConfig = array('properties' => 'ALL');
        }

        $entity = $this->formatBundleForEntity($entity, $bundleConfig);

        return $entity;
    }

    /**
     * Format an entity bundle
     *
     * @param object $entity
     * @param array $config
     * @return array
     */
    private function formatBundleForEntity($entity, $config)
    {
        try {
            $entityArray = $this->extract($entity);
        } catch (\Exception $ex) {
            return null;
        }

        $config = array_merge(array('properties' => 'ALL'), $config);

        if (isset($config['children'])) {

            $this->formatChildren($entity, $entityArray, $config);
        }

        $this->trimProperties($entityArray, $config['properties']);

        return $entityArray;
    }

    /**
     * Format the children of an entity
     *
     * @param array $entityArray
     * @param array $config
     * @return array
     */
    private function formatChildren($entity, &$entityArray, &$config)
    {
        if (is_null($config['properties']) && !empty($config['children'])) {
            $config['properties'] = array();
        }

        foreach ($config['children'] as $childName => $details) {

            if (is_numeric($childName) && is_string($details)) {
                $childName = $details;
                $details = array();
            }

            if (is_array($config['properties']) && !in_array($childName, $config['properties'])) {
                $config['properties'][] = $childName;
            }

            $getter = $this->formatGetter($childName);

            if (method_exists($entity, $getter)) {

                $children = $this->filterChildren($entity->$getter(), $details);

                $entityArray[$childName] = $this->formatChild($children, $details);
            }
        }
    }

    /**
     * Filter children
     *
     * @param type $children
     * @param type $details
     * @return type
     */
    private function filterChildren($children, $details)
    {
        if (!($children instanceof Collection)) {
            return $children;
        }

        if (isset($details['criteria']) && is_array($details['criteria'])) {

            if (isset($details['criteria']['_OPTIONS_'])) {
                $options = $details['criteria']['_OPTIONS_'];
                unset($details['criteria']['_OPTIONS_']);
            } else {
                $options = array();
            }

            foreach ($details['criteria'] as $field => $value) {

                $children = $this->applyCriteria($children, $field, $value, $options);
            }
        }

        return $children;
    }

    /**
     * Apply a single criteria item
     *
     * @param array $children
     * @param string $field
     * @param string $value
     * @param array $options
     * @return array
     */
    private function applyCriteria($children, $field, $value, $options)
    {
        if (isset($options['manualSearch'])) {
            return $this->manuallyApplyCriteria($children, $field, $value);
        }

        // Here we try to filter the children using doctrine's Criteria class
        // this can fail if the field is a foreign key and the value is not an object
        // so we need to fallback to iteration anyway

        try {

            $criteria = Criteria::create();
            $criteria->where(Criteria::expr()->eq($field, $value));
            $children = $children->matching($criteria);

        } catch (\Exception $ex) {

            $children = $this->manuallyApplyCriteria($children, $field, $value);
        }

        return $children;
    }

    /**
     * Apply a single criteria item manualy, bypassing doctrine
     *
     * @param array $children
     * @param string $field
     * @param string $value
     * @return array
     */
    private function manuallyApplyCriteria($children, $field, $value)
    {
        foreach ($children as $child) {

            // If the method doesn't exist just break
            if (!method_exists($child, 'get' . ucfirst($field))) {
                break;
            }

            $entity = $child->{'get' . ucfirst($field)}();

            if ($entity instanceof EntityInterface
                && method_exists($entity, 'getId')
                && $entity->getId() != $value) {
                $children->removeElement($child);
            }
        }

        return $children;
    }

    /**
     * Format a child/children
     *
     * @param mixed $children
     * @return array
     */
    private function formatChild($children, $details)
    {
        if ($children instanceof Collection) {

            $newChildren = array();

            foreach ($children as $child) {

                $newChildren[] = $this->formatBundleForEntity($child, $details);
            }
        } else {

            $newChildren = $this->formatBundleForEntity($children, $details);
        }

        return $newChildren;
    }

    /**
     * Workout the getter method name for a property
     *
     * @param string $property
     * @return string
     */
    private function formatGetter($property)
    {
        return 'get' . ucwords($property);
    }

    /**
     * Trim entity properties
     *
     * @param array $entityArray
     * @param array $properties
     * @return array
     */
    private function trimProperties(&$entityArray, $properties)
    {
        if ($properties === null) {
            $properties = array();
        }

        if (is_array($properties)) {

            foreach (array_keys($entityArray) as $property) {

                if (!in_array($property, $properties)) {
                    unset($entityArray[$property]);
                }
            }
        }
    }

    /**
     * Method to extract data
     *
     * @param object $entity
     * @return array
     */
    private function extract($entity)
    {
        if ($entity === null) {
            return array();
        }

        $hydrator = $this->getHydrator();

        $data = $hydrator->extract($entity);

        return $this->convertDates($data);
    }

    /**
     * Converts dates from DateTime to string for rest response
     *
     * @param array $data
     *
     * @return array
     */
    private function convertDates($data)
    {
        foreach ($data as &$column) {
            if ($column instanceof \DateTime) {
                $column = $column->format(\DateTime::ISO8601);
            }
        }
        return $data;
    }
}
