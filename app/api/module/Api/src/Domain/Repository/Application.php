<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Dvsa\Olcs\Api\Domain\Exception;

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

    public function fetchWithLicenceAndOc($applicationId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.operatingCentres', 'l_oc')
            ->with('l_oc.operatingCentre', 'l_oc_oc')
            ->with('l_oc_oc.address', 'l_oc_oc_a')
            ->with('operatingCentres', 'a_oc')
            ->with('a_oc.operatingCentre', 'a_oc_oc')
            ->with('a_oc_oc.address', 'a_oc_oc_a')
            ->byId($applicationId);

        return $qb->getQuery()->getSingleResult();
    }

    public function fetchWithLicence($applicationId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->byId($applicationId);

        $res = $qb->getQuery()->getResult();
        if (!$res) {
            throw new Exception\NotFoundException('Resource not found');
        }
        return $res[0];
    }

    /**
     * Override parent
     *
     * @param QueryBuilder $qb
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        // PMD
        unset($qb);

        $this->getQueryBuilder()
            ->with('licence', 'l');
    }

    /**
     * Override parent
     *
     * @param QueryBuilder $qb
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (is_numeric($query->getOrganisation())) {
            $qb->andWhere($qb->expr()->eq('l.organisation', ':organisation'))
                ->setParameter('organisation', $query->getOrganisation());
        }
    }
}
