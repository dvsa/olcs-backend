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
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

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
     * @param bool $outstanding   Only get fees that are outstanding
     *
     * @return array
     */
    public function fetchInterimFeesByApplicationId($applicationId, $outstanding = false)
    {
        $doctrineQb = $this->getQueryByApplicationFeeTypeFeeType(
            $applicationId,
            \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_GRANTINT
        );

        if ($outstanding) {
            $this->whereOutstandingFee($doctrineQb);
        }

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch outstanding fees for an organisation
     * (only those associated to a valid licence or in progress application)
     *
     * @param int $organisationId Organisation ID
     *
     * @return array
     */
    public function fetchOutstandingFeesByOrganisationId($organisationId)
    {
        $doctrineQb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($doctrineQb)
            ->withRefdata()
            ->with('licence')
            ->with('application')
            ->with('feePayments', 'fp')
            ->with('fp.payment', 'p')
            ->with('p.status')
            ->order('invoicedDate', 'ASC');

        $this->whereOutstandingFee($doctrineQb);
        $this->whereCurrentLicenceOrApplicationFee($doctrineQb, $organisationId);

        return $doctrineQb->getQuery()->getResult();
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
            ->innerJoin('f.feeType', 'ft')
            ->andWhere($doctrineQb->expr()->eq('f.application', ':application'))
            ->andWhere($doctrineQb->expr()->eq('ft.feeType', ':feeType'))
            ->setParameter('application', $applicationId)
            ->setParameter('feeType', RefData::FEE_TYPE_GRANT);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch outstanding continuation fees for a licence
     *
     * @param int $licenceId
     *
     * @return array
     */
    public function fetchOutstandingContinuationFeesByLicenceId($licenceId)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb
            ->innerJoin('f.feeType', 'ft')
            ->andWhere($doctrineQb->expr()->eq('f.licence', ':licence'))
            ->andWhere($doctrineQb->expr()->eq('ft.feeType', ':feeType'))
            ->setParameter('licence', $licenceId)
            ->setParameter('feeType', RefData::FEE_TYPE_CONT);

        $this->whereOutstandingFee($doctrineQb);

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
            ->with('feePayments', 'fp')
            ->with('fp.payment', 'p')
            ->with('p.status')
            ->order('invoicedDate', 'ASC');

        $this->whereOutstandingFee($doctrineQb);

        $doctrineQb
            ->andWhere($doctrineQb->expr()->in($this->alias . '.id', ':feeIds'))
            ->setParameter('feeIds', $ids);

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
        $doctrineQb->andWhere($doctrineQb->expr()->in('f.feeStatus', ':feeStatus'));

        $doctrineQb->setParameter(
            'feeStatus',
            [
                $this->getRefdataReference(Entity::STATUS_OUTSTANDING),
                $this->getRefdataReference(Entity::STATUS_WAIVE_RECOMMENDED),
            ]
        );
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
            ->leftJoin('f.application', 'a')
            ->leftJoin('f.licence', 'l')
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
                    $doctrineQb->expr()->isNotNull('f.licence'),
                    $doctrineQb->expr()->isNotNull('f.application')
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
            ->filterByIds($query->getIds());

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
            switch ($query->getStatus()) {
                case 'historical':
                    $feeStatus = [
                        Entity::STATUS_PAID,
                        Entity::STATUS_WAIVED,
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
                        Entity::STATUS_WAIVE_RECOMMENDED,
                    ];
                    break;
            }
            if (!empty($feeStatus)) {
                $qb
                    ->andWhere($qb->expr()->in($this->alias . '.feeStatus', ':feeStatus'))
                    ->setParameter('feeStatus', $feeStatus);
            }
        }

        $this->getQueryBuilder()->modifyQuery($qb)->withCreatedBy();
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
}
