<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'a';

    public function fetchWithPreviousConvictionsUsingId($query)
    {
        $qb = $this->createQueryBuilder();
        $this->buildDefaultQuery($qb, $query->getId())
            ->with('previousConvictions');

        return $qb->getQuery()->getSingleResult();

    }

    public function fetchForOrganisation($organisationId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('licence', 'l');

        $qb->andWhere($qb->expr()->eq('l.organisation', ':organisationId'))
            ->setParameter('organisationId', $organisationId);

        return $qb->getQuery()->execute();
    }

    public function fetchActiveForOrganisation($organisationId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('licence', 'l');

        $activeStatuses = [
            Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Entity::APPLICATION_STATUS_GRANTED,
        ];

        $qb
            ->andWhere($qb->expr()->eq('l.organisation', ':organisationId'))
            ->andWhere($qb->expr()->in($this->alias . '.status', $activeStatuses))
            ->setParameter('organisationId', $organisationId);

        return $qb->getQuery()->execute();
    }

    /**
     * Extend the default resource bundle to include licence
     *
     * @param QueryBuilder $qb
     * @param QryCmd $query
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->with('licence')->byId($id);
    }
}
