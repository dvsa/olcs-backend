<?php

/**
 * Abstract Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;

/**
 * Abstract Repository
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    protected $entity = 'Define\Me';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    private $queryBuilder;

    public function __construct(EntityManagerInterface $em, QueryBuilderInterface $queryBuilder)
    {
        $this->em = $em;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->getEntityManager()->getRepository($this->entity)->createQueryBuilder('a');
    }
}
