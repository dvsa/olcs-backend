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

    protected $qb;
    protected $selects = array();
    protected $joins = array();
    protected $params = array();

    public function setQueryBuilder($qb)
    {
        $this->qb = $qb;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function build($config, $name = 'main', $alias = 'm')
    {
        $this->addSelect($alias);

        if (isset($config['children'])) {
            foreach ($config['children'] as $childName => $childConfig) {

                if (is_numeric($childName) && is_string($childConfig)) {
                    $childName = $childConfig;
                    $childConfig = array('properties' => 'ALL');
                }

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

                $this->build($childConfig, $childName, $childAlias);
            }
        }

        if (isset($config['sort']) && isset($config['order'])) {
            $this->qb->addOrderBy($alias . '.' . $config['sort'], $config['order']);
        }
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
