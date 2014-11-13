<?php

/**
 * Bundle Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Utility;

/**
 * Bundle Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleQuery
{
    protected $qb;

    protected $selects = array();
    protected $joins = array();

    public function __construct($qb)
    {
        $this->qb = $qb;
    }

    public function build($bundleConfig)
    {
        $this->buildQueryFromBundle($bundleConfig);

        $selects = array('m');

        foreach ($this->selects as $alias => $properties) {
            if ($properties === null) {
                $selects[] = $alias;
            } else {
                foreach ($properties as $property) {
                    $selects[] = $alias . '.' . $property;
                }
            }
        }

        $this->qb->select($selects);

        foreach ($this->joins as $alias => $details) {
            // @todo criteria
            $this->qb->leftJoin($details['relationship'], $alias);
        }
    }

    protected function buildQueryFromBundle($config, $name = 'main', $prefix = '')
    {
        if ($prefix == '' || !isset($config['properties']) || !is_array($config['properties'])) {
            $config['properties'] = null;
        }

        $alias = $this->getSelectAlias($name, $prefix);
        $this->addSelect($alias, $config['properties']);

        if (isset($config['children'])) {
            foreach ($config['children'] as $childName => $childConfig) {

                $childAlias = $this->buildQueryFromBundle($childConfig, $childName, $alias);

                $this->addJoin($alias, $childName, $childAlias, $childConfig);
            }
        }

        return $alias;
    }

    protected function addJoin($alias, $childName, $childAlias, $childConfig)
    {
        $this->joins[$childAlias] = array(
            'relationship' => $alias . '.' . $childName,
            'criteria' => null // @todo this
        );
    }

    protected function getSelectAlias($name, $prefix)
    {
        $aliasPrefix = $prefix . substr($name, 0, 1);

        $alias = $aliasPrefix;
        $i = 0;

        while (array_key_exists($alias, $this->selects)) {
            $alias = $aliasPrefix . $i;
            $i++;
        }

        return $alias;
    }

    protected function addSelect($alias, $properties = null)
    {
        $this->selects[$alias] = $properties;
    }
}
