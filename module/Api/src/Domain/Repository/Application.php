<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Application\Application as Entity;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application extends AbstractRepository
{
    protected $entity = Entity::class;

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
            ->withRefData()
            ->with('licence', 'l');

        $qb
            ->andWhere($qb->expr()->eq('l.organisation',':organisationId'))
            ->setParameter('organisationId', $organisationId);

        return $qb->getQuery()->execute();
    }
}
