<?php

/**
 * Query Builder
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Query Builder
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 *
 * @method QueryBuilder withRefdata($entity = null, $alias = null)
 * @method QueryBuilder byId($id)
 * @method QueryBuilder with($property, $alias = null)
 */
/*final */class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var DoctrineQueryBuilder
     */
    private $qb;

    /**
     * @var ServiceLocatorInterface
     */
    private $queryPartialServiceManager;

    public function __construct(ServiceLocatorInterface $queryPartialServiceManager)
    {
        $this->queryPartialServiceManager = $queryPartialServiceManager;
    }

    /**
     * @return QueryBuilder
     */
    public function modifyQuery(DoctrineQueryBuilder $qb)
    {
        $this->qb = $qb;

        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function __call($name, $arguments)
    {
        $this->queryPartialServiceManager->get(ucfirst($name))->modifyQuery($this->qb, $arguments);

        return $this;
    }
}
