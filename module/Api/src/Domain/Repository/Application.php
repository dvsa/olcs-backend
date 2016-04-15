<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Doctrine\ORM\Query\Expr\Join;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'a';

    /**
     * @param int $organisationId
     */
    public function fetchActiveForOrganisation($organisationId)
    {
        $activeStatuses = [
            Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Entity::APPLICATION_STATUS_GRANTED,
        ];

        return $this->fetchByOrganisationIdAndStatuses($organisationId, $activeStatuses);
    }

    public function fetchByOrganisationIdAndStatuses($organisationId, $statuses)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata();

        $qb
            ->innerJoin('a.licence', 'l', Join::WITH, $qb->expr()->eq('l.organisation', ':organisationId'))
            ->andWhere($qb->expr()->in($this->alias . '.status', $statuses))
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

    public function fetchWithLicenceAndOrg($applicationId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.organisation', 'l_org')
            ->byId($applicationId);

        $res = $qb->getQuery()->getResult();
        if (!$res) {
            throw new Exception\NotFoundException('Resource not found');
        }
        return $res[0];
    }

    public function fetchWithTmLicences($applicationId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.tmLicences', 'ltml')
            ->byId($applicationId);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Override parent
     *
     * @param QueryBuilder $qb
     * @inheritdoc
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
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

    public function fetchForNtu()
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('l.trafficArea', 'lta')
            ->with('fees', 'f')
            ->with('f.feeType', 'ft')
            ->with('f.feeStatus', 'fs');

        $qb->andWhere($qb->expr()->eq($this->alias . '.status', ':appStatus'));
        $qb->setParameter('appStatus', Entity::APPLICATION_STATUS_GRANTED);

        $qb->andWhere($qb->expr()->eq('f.feeStatus', ':feeStatus'));
        $qb->setParameter('feeStatus', FeeEntity::STATUS_OUTSTANDING);

        $qb->andWhere($qb->expr()->in('ft.feeType', ':feeType'));
        $qb->setParameter('feeType', [FeeTypeEntity::FEE_TYPE_GRANT, FeeTypeEntity::FEE_TYPE_GRANTVAR]);

        return $qb->getQuery()->getResult();
    }
}
