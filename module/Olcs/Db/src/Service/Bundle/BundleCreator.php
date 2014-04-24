<?php

/**
 * BundleCreator class, handles the bundling of entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\Bundle;

use Doctrine\Common\Collections\Collection;

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
        $bundleConfig =  null;

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
        $entityArray = $this->extract($entity);

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

                $children = $entity->$getter();

                $entityArray[$childName] = $this->formatChild($children, $details);
            }
        }
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
