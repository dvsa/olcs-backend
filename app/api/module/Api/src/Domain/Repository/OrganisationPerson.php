<?php

/**
 * Organisation Person
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as Entity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;

/**
 * Organisation Person
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationPerson extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchByOrgAndPerson(OrganisationEntity $organisation, PersonEntity $person)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq('m.organisation', $organisation->getId())
        );

        $qb->andWhere(
            $qb->expr()->eq('m.person', $person->getId())
        );

        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult();
    }

    /**
     * Fetch a list for an Organisation
     *
     * @param int $organisationId
     *
     * @return array of Entity
     */
    public function fetchListForOrganisation($organisationId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('person', 'p')
            ->with('p.title');

        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisationId'))
            ->setParameter('organisationId', $organisationId);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch a list for an Organisation and a Person
     *
     * @param int $organisationId
     * @param int $personId
     *
     * @return array of Entity
     */
    public function fetchListForOrganisationAndPerson($organisationId, $personId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('person', 'p')
            ->with('p.title');

        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':organisationId'))
            ->setParameter('organisationId', $organisationId);
        $qb->andWhere($qb->expr()->eq($this->alias . '.person', ':personId'))
            ->setParameter('personId', $personId);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch a list for a Person
     *
     * @param int $personId
     *
     * @return array of Entity
     */
    public function fetchListForPerson($personId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('person', 'p')
            ->with('p.title');

        $qb->andWhere($qb->expr()->eq($this->alias . '.person', ':personId'))
            ->setParameter('personId', $personId);

        return $qb->getQuery()->getResult();
    }
}
