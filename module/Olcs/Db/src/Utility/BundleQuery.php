<?php

/**
 * Bundle Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Utility;

use Doctrine\ORM\Query\Expr\Join;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Bundle Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleQuery implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $qb;
    protected $selects = array();
    protected $joins = array();
    protected $params = array();

    protected $nesting = [];
    protected $refDataReplacements = array();
    protected $refDataClassName = 'Olcs\\Db\\Entity\\RefData';
    protected $refDataAlias = 1;

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

    public function build($config, $name = 'main', $alias = 'm', $parent = null, $stack = [])
    {
        if ($parent === null) {
            $parent = $this->qb->getRootEntities()[0];
        }

        $metadata = $this->qb->getEntityManager()->getClassMetadata($parent);

        $this->addSelect($alias);

        if (isset($config['children'])) {

            $processed = [];
            foreach ($config['children'] as $childName => $childConfig) {

                if (is_numeric($childName) && is_string($childConfig)) {

                    if (in_array($childConfig, $processed)) {
                        continue;
                    }
                    $processed[] = $childConfig;
                    $childName = $childConfig;
                    $childConfig = [];
                }

                // Build the stack
                $childStack = $stack;
                $childStack[] = $childName;

                if ($this->isRefData($metadata, $childName)) {
                    $refDataAlias = $this->getUniqueAlias();
                    $this->qb->addSelect(['IDENTITY(' . $alias . '.' . $childName . ', \'id\') AS ' . $refDataAlias]);
                    $this->refDataReplacements[] = [
                        'stack' => $childStack,
                        'valueAlias' => $refDataAlias
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

                $this->build($childConfig, $childName, $childAlias, $entityClass, $childStack);
            }
        }
    }

    protected function getUniqueAlias()
    {
        return 'RD_' . $this->refDataAlias++;
    }

    protected function getChildClass($metadata, $name)
    {
        return $metadata->associationMappings[$name]['targetEntity'];
    }

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
