<?php

/**
 * Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Fee\Fee as Entity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Doctrine\ORM\Query;

/**
 * Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Fee extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'f';

    /**
     * Gets the latest bus reg fee
     *
     * @param $busRegId
     * @return Fee|null
     */
    public function getLatestFeeForBusReg($busRegId)
    {
        $doctrineQb = $this->createQueryBuilder();
        $this->getQueryBuilder()->withRefdata()->order('invoicedDate', 'DESC');
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.busReg', ':busRegId'));
        $doctrineQb->setParameter('busRegId', $busRegId);
        $doctrineQb->setMaxResults(1);

        $results = $doctrineQb->getQuery()->getResult();

        return !empty($results) ? $results[0] : null;
    }

    /**
     * Fetch application interim fees
     *
     * @param int  $applicationId Application ID
     * @param bool $outstanding Include fees that are outstanding
     * @param bool $paid Include fees that are paid
     *
     * @return array
     */
    public function fetchInterimFeesByApplicationId($applicationId, $outstanding = false, $paid = false)
    {
        $doctrineQb = $this->getQueryByApplicationFeeTypeFeeType(
            $applicationId,
            \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_GRANTINT
        );

        if ($outstanding) {
            $this->whereOutstandingFee($doctrineQb);
        }
        if ($paid) {
            $this->wherePaidFee($doctrineQb);
        }

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch outstanding fees for an organisation
     * (only those associated to a valid licence or in progress application)
     *
     * @param int $organisationId Organisation ID
     * @param bool $hideExpired
     * @return array
     */
    public function fetchOutstandingFeesByOrganisationId($organisationId, $hideExpired = false)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($doctrineQb)
            ->withRefdata()
            ->with('feeTransactions', 'ft')
            ->with('ft.transaction', 't')
            ->with('t.status')
            ->order('invoicedDate', 'ASC');

        $this->whereOutstandingFee($doctrineQb);
        $this->whereCurrentLicenceOrApplicationFee($doctrineQb, $organisationId);

        if ($hideExpired) {
            $this->hideExpired($doctrineQb);
        }

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * @param int $organisationId
     * @return int
     */
    public function getOutstandingFeeCountByOrganisationId($organisationId, $hideExpired = false)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->select('COUNT(f)');
        $this->whereOutstandingFee($doctrineQb);

        // @todo this is still slow, might be better doing 2 queries rather
        // than an OR
        $this->whereCurrentLicenceOrApplicationFee($doctrineQb, $organisationId);

        if ($hideExpired) {
            $this->hideExpired($doctrineQb);
        }

        return $doctrineQb->getQuery()->getSingleScalarResult();
    }

    /**
     * Add a check to hide expired fees
     *
     * @param QueryBuilder $doctrineQb
     */
    protected function hideExpired($doctrineQb)
    {
        $doctrineQb->andWhere(
            $doctrineQb->expr()->orX(
                $doctrineQb->expr()->isNull('l.expiryDate'),
                $doctrineQb->expr()->gte('l.expiryDate', ':today')
            )
        )->setParameter('today', new DateTime());
    }

    /**
     * Fetch outstanding fees for an application
     *
     * @param int $applicationId Application ID
     *
     * @return array
     */
    public function fetchOutstandingFeesByApplicationId($applicationId)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->whereOutstandingFee($doctrineQb);

        $doctrineQb
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.application', ':application'))
            ->setParameter('application', $applicationId);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch outstanding grant fees for an application
     *
     * @param int $applicationId Application ID
     *
     * @return array
     */
    public function fetchOutstandingGrantFeesByApplicationId($applicationId)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->whereOutstandingFee($doctrineQb);

        $doctrineQb
            ->innerJoin($this->alias.'.feeType', 'ft')
            ->andWhere($doctrineQb->expr()->eq($this->alias.'.application', ':application'))
            ->andWhere($doctrineQb->expr()->eq('ft.feeType', ':feeType'))
            ->setParameter('application', $applicationId)
            ->setParameter('feeType', RefDataEntity::FEE_TYPE_GRANT);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch outstanding continuation fees for a licence
     *
     * @param int $licenceId
     * @param DateTime $after if specified, only return fees with invoicedDate
     * after this value
     *
     * @return array
     */
    public function fetchOutstandingContinuationFeesByLicenceId($licenceId, $after = null)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb
            ->innerJoin($this->alias.'.feeType', 'ft')
            ->andWhere($doctrineQb->expr()->eq($this->alias.'.licence', ':licence'))
            ->andWhere($doctrineQb->expr()->eq('ft.feeType', ':feeType'))
            ->setParameter('licence', $licenceId)
            ->setParameter('feeType', RefDataEntity::FEE_TYPE_CONT);

        $this->whereOutstandingFee($doctrineQb);

        if (!is_null($after)) {
            $doctrineQb
                ->andWhere($doctrineQb->expr()->gte($this->alias.'.invoicedDate', ':after'))
                ->setParameter('after', $after);
        }

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch outstanding fees by IDs
     *
     * @param array $ids
     *
     * @return array
     */
    public function fetchOutstandingFeesByIds($ids)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($doctrineQb)
            ->withRefdata()
            ->with('licence')
            ->with('application')
            ->with('feeTransactions', 'ft')
            ->with('ft.transaction', 't')
            ->with('t.status')
            ->order('invoicedDate', 'ASC');

        $this->whereOutstandingFee($doctrineQb);

        $doctrineQb
            ->andWhere($doctrineQb->expr()->in($this->alias . '.id', ':feeIds'))
            ->setParameter('feeIds', $ids);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch fees by irfoGvPermitId
     *
     * @param int $irfoGvPermitId
     *
     * @return array
     */
    public function fetchFeesByIrfoGvPermitId($irfoGvPermitId)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($doctrineQb)
            ->withRefdata()
            ->order('invoicedDate', 'ASC');

        $doctrineQb
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.irfoGvPermit', ':irfoGvPermitId'))
            ->setParameter('irfoGvPermitId', $irfoGvPermitId);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch all fees by irfoPsvAuthId
     *
     * @param int $irfoPsvAuthId
     * @param bool $outstanding Only get fees that are outstanding
     *
     * @return array
     */
    public function fetchFeesByIrfoPsvAuthId($irfoPsvAuthId, $outstanding = false)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($doctrineQb)
            ->withRefdata()
            ->order('invoicedDate', 'ASC');

        $doctrineQb
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.irfoPsvAuth', ':irfoPsvAuthId'))
            ->setParameter('irfoPsvAuthId', $irfoPsvAuthId);

        if ($outstanding) {
            $this->whereOutstandingFee($doctrineQb);
        }

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch Application fee by irfoPsvAuthId
     *
     * @param int $irfoPsvAuthId
     *
     * @return array
     */
    public function fetchApplicationFeeByPsvAuthId($irfoPsvAuthId)
    {
        $fees = $this->fetchFeesByPsvAuthIdAndType($irfoPsvAuthId, RefDataEntity::FEE_TYPE_IRFOPSVAPP);

        if (count($fees) > 0) {
            return $fees[0];
        }
        return null;
    }

    /**
     * Fetch fees by irfoPsvAuthId
     *
     * @param int $irfoPsvAuthId
     * @param $feeTypeFeeType
     * @return array
     */
    public function fetchFeesByPsvAuthIdAndType($irfoPsvAuthId, $feeTypeFeeType)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($doctrineQb)
            ->withRefdata();
        $this->getQueryBuilder()->order('invoicedDate', 'DESC');
        $doctrineQb->join($this->alias . '.feeType', 'ft')
            ->andWhere($doctrineQb->expr()->eq('ft.feeType', ':feeTypeFeeType'))
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.irfoPsvAuth', ':irfoPsvAuthId'))
            ->setParameter('irfoPsvAuthId', $irfoPsvAuthId)
            ->setParameter('feeTypeFeeType', $this->getRefdataReference($feeTypeFeeType));

        return $doctrineQb->getQuery()->getResult();
    }

    public function fetchLatestFeeByTypeStatusesAndApplicationId(
        $feeType,
        $feeStatuses,
        $applicationId
    ) {
        $doctrineQb = $this->createQueryBuilder();
        $this->getQueryBuilder()->withRefdata()->order('invoicedDate', 'DESC');
        $doctrineQb
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.application', ':application'))
            ->andWhere($doctrineQb->expr()->in($this->alias . '.feeStatus', ':feeStatuses'))
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.feeType', ':feeType'))
            ->setParameter('application', $applicationId)
            ->setParameter('feeStatuses', $feeStatuses)
            ->setParameter('feeType', $feeType)
            ->setMaxResults(1);

        $results = $doctrineQb->getQuery()->getResult();

        if (!empty($results)) {
            return $results[0];
        }
    }

    /**
     * Get a QueryBuilder for listing application fees of a certain feeType.feeType
     *
     * @param int    $applicationId  Application ID
     * @param string $feeTypeFeeType Ref data string eg \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_GRANTINT
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryByApplicationFeeTypeFeeType($applicationId, $feeTypeFeeType)
    {
        $doctrineQb = $this->createQueryBuilder();
        $this->getQueryBuilder()->withRefdata()->order('invoicedDate', 'ASC');

        $doctrineQb->join($this->alias . '.feeType', 'ft')
            ->andWhere($doctrineQb->expr()->eq('ft.feeType', ':feeTypeFeeType'))
            ->andWhere($doctrineQb->expr()->eq($this->alias . '.application', ':applicationId'));

        $doctrineQb->setParameter('feeTypeFeeType', $this->getRefdataReference($feeTypeFeeType))
            ->setParameter('applicationId', $applicationId);

        return $doctrineQb;
    }

    /**
     * Add conditions to the query builder to only select fees that are outstanding
     *
     * @param \Doctrine\ORM\QueryBuilder $doctrineQb
     */
    private function whereOutstandingFee($doctrineQb)
    {
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias.'.feeStatus', ':feeStatus'));

        $doctrineQb->setParameter('feeStatus', $this->getRefdataReference(Entity::STATUS_OUTSTANDING));
    }

    /**
     * Add conditions to the query builder to only select fees that are paid
     *
     * @param \Doctrine\ORM\QueryBuilder $doctrineQb
     */
    private function wherePaidFee($doctrineQb)
    {
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias.'.feeStatus', ':feeStatus'));

        $doctrineQb->setParameter('feeStatus', $this->getRefdataReference(Entity::STATUS_PAID));
    }

    /**
     * Add conditions to the query builder to only select fees that are associated
     * to either:
     *  a) a valid/curtailed/suspended licence
     *  or
     *  b) an under consideration/granted application
     * for the given organisation
     *
     * @param \Doctrine\ORM\QueryBuilder $doctrineQb
     * @param int $organisationId
     */
    private function whereCurrentLicenceOrApplicationFee($doctrineQb, $organisationId)
    {
        $doctrineQb
            ->leftJoin($this->alias.'.application', 'a')
            ->leftJoin($this->alias.'.licence', 'l')
            ->leftJoin('a.licence', 'al')
            ->andWhere(
                $doctrineQb->expr()->orX(
                    $doctrineQb->expr()->eq('l.organisation', ':organisationId'),
                    $doctrineQb->expr()->eq('al.organisation', ':organisationId')
                )
            )
            ->andWhere(
                $doctrineQb->expr()->orX(
                    $doctrineQb->expr()->in('a.status', ':appStatus'),
                    $doctrineQb->expr()->in('l.status', ':licStatus')
                )
            )
            ->andWhere(
                $doctrineQb->expr()->orX(
                    $doctrineQb->expr()->isNotNull($this->alias.'.licence'),
                    $doctrineQb->expr()->isNotNull($this->alias.'.application')
                )
            )
            ->setParameter('organisationId', $organisationId)
            ->setParameter(
                'appStatus',
                [
                    $this->getRefdataReference(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION),
                    $this->getRefdataReference(ApplicationEntity::APPLICATION_STATUS_GRANTED),
                ]
            )
            ->setParameter(
                'licStatus',
                [
                    $this->getRefdataReference(LicenceEntity::LICENCE_STATUS_VALID),
                    $this->getRefdataReference(LicenceEntity::LICENCE_STATUS_CURTAILED),
                    $this->getRefdataReference(LicenceEntity::LICENCE_STATUS_SUSPENDED),
                ]
            );
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $this->getQueryBuilder()
            ->filterByLicence($query->getLicence())
            ->filterByApplication($query->getApplication())
            ->filterByIds(!empty($query->getIds()) ? $query->getIds() : null);

        if ($query->getOrganisation() !== null) {
            // all fees linked to the organisation by irfo_gv_permit_id or irfo_psv_auth_id
            $qb
                ->leftJoin($this->alias . '.irfoGvPermit', 'igp')
                ->leftJoin($this->alias . '.irfoPsvAuth', 'ipa')
                // This where clause make the query run quicker by hinting to MySQL to use the indexes
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->isNotNull($this->alias . '.irfoGvPermit'),
                        $qb->expr()->isNotNull($this->alias . '.irfoPsvAuth')
                    )
                )
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('igp.organisation', ':organisationId'),
                        $qb->expr()->eq('ipa.organisation', ':organisationId')
                    )
                )
                ->setParameter('organisationId', $query->getOrganisation());
        }

        if ($query->getBusReg() !== null) {
            /** @var array $busRegIds */
            $busRegIds = $this->getBusRegIdsForRouteFromBusRegId($query->getBusReg());
            $qb
                ->andWhere($qb->expr()->in($this->alias . '.busReg', ':busRegIds'))
                ->setParameter('busRegIds', $busRegIds);
        }

        if ($query->getTask() !== null) {
            $qb->andWhere($this->alias . '.task = :taskId');
            $qb->setParameter('taskId', $query->getTask());
        }

        if ($query->getIrfoGvPermit() !== null) {
            $qb->andWhere($this->alias . '.irfoGvPermit = :irfoGvPermitId');
            $qb->setParameter('irfoGvPermitId', $query->getIrfoGvPermit());
        }

        if ($query->getIsMiscellaneous() !== null) {
            $qb->innerJoin($this->alias . '.feeType', 'ft')
                ->andWhere('ft.isMiscellaneous = :isMiscellaneous')
                ->setParameter('isMiscellaneous', $query->getIsMiscellaneous());
        }

        if ($query->getStatus() !== null) {
            $this->filterByStatus($qb, $query->getStatus());
        }

        $this->getQueryBuilder()->modifyQuery($qb)->withCreatedBy();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param string $status
     */
    private function filterByStatus($qb, $status)
    {
        switch ($status) {
            case 'historical':
                $feeStatus = [
                    Entity::STATUS_PAID,
                    Entity::STATUS_CANCELLED,
                ];
                break;
            case 'all':
                $feeStatus = [];
                break;
            case 'current':
            default:
                $feeStatus = [
                    Entity::STATUS_OUTSTANDING,
                ];
                break;
        }

        if (!empty($feeStatus)) {
            $qb
                ->andWhere($qb->expr()->in($this->alias . '.feeStatus', ':feeStatus'))
                ->setParameter('feeStatus', $feeStatus);
        }
    }

    /**
     * Get all bus reg ids for a route no
     *
     * @param int $busRegId
     * @return array
     */
    protected function getBusRegIdsForRouteFromBusRegId($busRegId)
    {
        $qb = $this->getEntityManager()
            ->getRepository(BusRegEntity::class)
            ->createQueryBuilder('br');

        $qb
            ->select('br2.id')
            ->join(BusRegEntity::class, 'br2')
            ->where('br.routeNo = br2.routeNo')
            ->andWhere('br.id = :id')
            ->setParameter('id', $busRegId);

        return $qb->getQuery()->getArrayResult();
    }

    public function fetchLatestFeeByApplicationId($applicationId)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()
            ->withRefdata()
            ->with('feeTransactions', 'ft')
            ->with('ft.transaction', 't')
            ->order('invoicedDate', 'DESC');

        $qb->andWhere($qb->expr()->eq($this->alias . '.application', ':application'))
            ->setParameter('application', $applicationId)
            ->setMaxResults(1);

        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
        return count($results) ? $results[0] : [];
    }
}
