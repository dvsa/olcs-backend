<?php

/**
 * ApplicationOrganisationPerson
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Application\ApplicationOrganisationPerson as Entity;

/**
 * ApplicationOrganisationPerson
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationOrganisationPerson extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch a list for an Application
     *
     * @param int $applicationId
     *
     * @return array of Entity
     */
    public function fetchListForApplication($applicationId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('person', 'p')
            ->with('p.title');

        $qb->andWhere($qb->expr()->eq($this->alias . '.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch an entity for an Application and Person
     *
     * @param int $applicationId
     * @param int $personId
     *
     * @return Entity
     */
    public function fetchForApplicationAndPerson($applicationId, $personId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('person', 'p')
            ->with('p.title');

        $qb->andWhere($qb->expr()->eq($this->alias . '.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);
        $qb->andWhere($qb->expr()->eq($this->alias . '.person', ':personId'))
            ->setParameter('personId', $personId);

        $results = $qb->getQuery()->getResult();
        if (empty($results)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * Fetch an entity for an Application and OriginalPerson
     *
     * @param int $applicationId
     * @param int $personId
     *
     * @return Entity
     */
    public function fetchForApplicationAndOriginalPerson($applicationId, $personId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('person', 'p')
            ->with('p.title');

        $qb->andWhere($qb->expr()->eq($this->alias . '.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);
        $qb->andWhere($qb->expr()->eq($this->alias . '.originalPerson', ':personId'))
            ->setParameter('personId', $personId);

        $results = $qb->getQuery()->getResult();
        if (empty($results)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }
}
