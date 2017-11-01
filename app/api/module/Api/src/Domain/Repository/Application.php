<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceE;
use Dvsa\Olcs\Transfer\Query as TransferQry;

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
     * Fetch Active For Organisation
     *
     * @param int $organisationId Organisation id
     *
     * @return array
     */
    public function fetchActiveForOrganisation($organisationId)
    {
        $activeStatuses = [
            Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Entity::APPLICATION_STATUS_GRANTED,
        ];

        return $this->fetchByOrganisationIdAndStatuses($organisationId, $activeStatuses);
    }

    /**
     * Prepare Query builder to Fetch by Organisation and statuses
     *
     * @param int   $orgId    Organisation Id
     * @param array $statuses List of Application statuses
     *
     * @return QueryBuilder
     */
    private function prepareFetchByOrgAndStatus($orgId, array $statuses = [])
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata();

        $qb
            ->innerJoin('a.licence', 'l', Join::WITH, $qb->expr()->eq('l.organisation', ':organisationId'))
            ->andWhere($qb->expr()->in($this->alias . '.status', $statuses))
            ->setParameter('organisationId', $orgId);

        return $qb;
    }

    /**
     * Fetch by Organisation and statuses
     *
     * @param int   $orgId    Organisation Id
     * @param array $statuses List of Application statuses
     *
     * @return array
     */
    public function fetchByOrgAndStatusForActiveLicences($orgId, array $statuses = [])
    {
        $qb = $this->prepareFetchByOrgAndStatus($orgId, $statuses);

        $qbE = $qb->expr();

        $qb->andWhere(
            $qbE->orX(
                $qbE->eq($this->alias . '.isVariation', 0),
                $qbE->andX(
                    $qbE->in('l.status', LicenceE::ACTIVE_STATUSES),
                    $qbE->eq($this->alias . '.isVariation', 1),
                    $qbE->orX(
                        $qbE->isNull($this->alias . '.variationType'),
                        $qbE->neq($this->alias . '.variationType', ':directorChangeVariationType')
                    )
                )
            )
        )
        ->setParameter('directorChangeVariationType', Entity::VARIATION_TYPE_DIRECTOR_CHANGE);

        return $qb->getQuery()->execute();
    }

    /**
     * Fetch by Organisation and statuses
     *
     * @param int   $orgId    Organisation Id
     * @param array $statuses List of Application statuses
     *
     * @return array
     */
    public function fetchByOrganisationIdAndStatuses($orgId, array $statuses = [])
    {
        $qb = $this->prepareFetchByOrgAndStatus($orgId, $statuses);

        return $qb->getQuery()->execute();
    }

    /**
     * Extend the default resource bundle to include licence
     *
     * @param QueryBuilder $qb Query Builder
     * @param int          $id Id
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->with('licence')->byId($id);
    }

    /**
     * Fetch With Licence And Oc
     *
     * @param int $applicationId Application Id
     *
     * @return mixed
     */
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

    /**
     * Fetch With Licence
     *
     * @param int $applicationId Application Id
     *
     * @return Entity
     * @throws Exception\NotFoundException
     */
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
     * Fetch With Licence And Org
     *
     * @param int $applicationId Application Id
     *
     * @return Entity
     * @throws Exception\NotFoundException
     */
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

    /**
     * Fetch With TM Licence
     *
     * @param int $applicationId Application Id
     *
     * @return Entity
     */
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
     * @param QueryBuilder $qb Query Builder
     *
     * @return void
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()
            ->with('licence', 'l');
    }

    /**
     * Override parent
     *
     * @param QueryBuilder               $qb    Doctrine Query Builder
     * @param TransferQry\QueryInterface $query Http Query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, TransferQry\QueryInterface $query)
    {
        if (method_exists($query, 'getOrganisation') && is_numeric($query->getOrganisation())) {
            $qb->andWhere($qb->expr()->eq('l.organisation', ':organisation'))
                ->setParameter('organisation', $query->getOrganisation());
        }

        if (method_exists($query, 'getStatus') && !empty($query->getStatus())) {
            $qb
                ->andWhere(
                    $qb->expr()->eq($this->alias . '.status', ':STATUS')
                )
                ->setParameter('STATUS', $query->getStatus());
        }
    }

    /**
     * Fetch For Ntu
     *
     * @return array
     */
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
