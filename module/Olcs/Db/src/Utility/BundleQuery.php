<?php

/**
 * Bundle Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Utility;

use Doctrine\ORM\Query\Expr\Join;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Bundle Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleQuery
{

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $qb;
    protected $selects = array();
    protected $joins = array();
    protected $params = array();

    protected $nesting = [];
    protected $refDataReplacements = array();
    protected $refDataClassName = 'Dvsa\\Olcs\\Api\\Entity\\System\\RefData';
    protected $refDataAlias = 1;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setQueryBuilder($qb)
    {
        $this->qb = $qb;
    }

    public function getParams()
    {
        return $this->params;
    }


    public function getRefDataReplacements()
    {
        return $this->refDataReplacements;
    }

    /**
     * Build is called recursively until it has iterated through the children in the bundle
     *
     * @param array $config Partial bundle
     * @param string $name Name of child (or main if it's the first call)
     * @param string $alias Alias of this node
     * @param string $parent Class name of the parent
     * @param array $stack The stack of nodes from the parents
     */
    public function build($config, $alias = 'm', $parent = null, $stack = [], $checkIsRefdata = true)
    {
        $this->addSelect($alias);

        if (isset($config['sort']) && isset($config['order'])) {
            $this->qb->addOrderBy($alias . '.' . $config['sort'], $config['order']);
        }

        if (!isset($config['children'])) {
            return;
        }

        // This is the first call to build, so find the root entity
        if ($parent === null) {
            $parent = $this->qb->getRootEntities()[0];
        }

        // grab the metadata of the parent entity
        $metadata = $this->qb->getEntityManager()->getClassMetadata($parent);

        $processed = [];
        foreach ($config['children'] as $childName => $childConfig) {

            $newCheckIsRefdata = true;

            // If our child is going to be a list, we don't want to check it's refdata
            if (isset($metadata->associationMappings[$childName]['isOwningSide'])
                && !$metadata->associationMappings[$childName]['isOwningSide']
            ) {
                $newCheckIsRefdata = false;
            }

            if (is_numeric($childName) && is_string($childConfig)) {

                // We skip children that have already been joined
                if (in_array($childConfig, $processed)) {
                    continue;
                }
                $processed[] = $childConfig;
                $childName = $childConfig;
                $childConfig = [];
            }

            // Add to the stack, so we have a reference to any refData items
            $childStack = $stack;
            $childStack[] = $childName;

            // If we find a refData relationship
            // Store the stack reference to it's location and skip it
            if (/*$checkIsRefdata &&*/ $this->isRefData($metadata, $childName)) {
                $this->refDataReplacements[] = [
                    'stack' => $childStack
                ];
                continue;
            }

            $entityClass = $this->getChildClass($metadata, $childName);

            $childAlias = $this->getSelectAlias($childName, $alias);

            $joinType = 'left';

            if (isset($childConfig['required'])) {
                $joinType = 'inner';
            }

            // @NOTE Not an ideal solution, but what we are saying here is where there are no results fetched
            // back in the leftJoin, this only works when the child has an ID column
            if (isset($childConfig['requireNone'])) {
                $this->qb->andWhere($childAlias . '.id IS NULL');
            }

            $this->addJoin($alias, $childName, $childAlias, $childConfig, $joinType);

            $this->build($childConfig, $childAlias, $entityClass, $childStack, $newCheckIsRefdata);
        }
    }

    /**
     * Grab the child's class name
     *
     * @param object $metadata
     * @param string $name
     * @return string
     */
    protected function getChildClass($metadata, $name)
    {
        return $metadata->associationMappings[$name]['targetEntity'];
    }

    /**
     * Check if the given node is a refdata node
     * (We ignore *ToMany relationships)
     *
     * @param object $metadata
     * @param string $name
     * @return boolean
     */
    protected function isRefData($metadata, $name)
    {
        return (
            isset($metadata->associationMappings[$name]['targetEntity'])
            && $metadata->associationMappings[$name]['targetEntity'] === $this->refDataClassName
            && !isset($metadata->associationMappings[$name]['joinTable'])
        );
    }

    protected function addJoin($alias, $childName, $childAlias, $childConfig, $joinType = 'left')
    {
        $conditionType = null;
        $condition = null;

        if (isset($childConfig['criteria'])) {
            $conditionType = Join::WITH;
            $condition = $this->buildCriteria($childAlias, $childConfig['criteria']);
        }

        $this->qb->{$joinType . 'Join'}($alias . '.' . $childName, $childAlias, $conditionType, $condition);
    }

    protected function buildCriteria($alias, $criteria)
    {
        $eb = $this->getServiceLocator()->get('ExpressionBuilder');

        $eb->setQueryBuilder($this->qb);
        $eb->setEntityManager($this->qb->getEntityManager());
        $eb->setParams($this->params);

        $expression = $eb->buildWhereExpression($criteria, $alias);

        $this->params = $eb->getParams();

        return $expression;
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

    protected function addSelect($alias)
    {
        $this->selects[$alias] = $alias;
        $this->qb->addSelect($alias);
    }
}
