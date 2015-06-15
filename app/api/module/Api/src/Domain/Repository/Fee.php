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
     * @return array
     */
    public function getLatestFeeForBusReg($busRegId)
    {
        $doctrineQb = $this->createQueryBuilder();
        $this->getQueryBuilder()->withRefdata()->order('invoicedDate', 'DESC');
        $doctrineQb->andWhere($doctrineQb->expr()->eq('f.busReg', ':busRegId'));
        $doctrineQb->setParameter('busRegId', $busRegId);
        $doctrineQb->setMaxResults(1);

        return $doctrineQb->getQuery()->getResult();
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

    /**
     * Get a QueryBuilder for listing application fees of a certain feeType.feeType
     *
     * @param int    $applicationId  Application ID
     * @param string $feeTypeFeeType Ref data string eg \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_GRANTINT
     *
     * @return Doctrine\ORM\QueryBuilder
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
     * @param Doctrine\ORM\QueryBuilder $doctrineQb
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
     * @param Doctrine\ORM\QueryBuilder $doctrineQb
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
        if ($query->getLicence() !== null) {
            $qb->andWhere($this->alias . '.licence = :licenceId');
            $qb->setParameter('licenceId', $query->getLicence());
        }

        if ($query->getApplication() !== null) {
            $qb->andWhere($this->alias . '.application = :applicationId');
            $qb->setParameter('applicationId', $query->getApplication());
        }

        if ($query->getBusReg() !== null) {
            $qb->andWhere($this->alias . '.busReg = :busRegId');
            $qb->setParameter('busRegId', $query->getBusReg());
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

        if (!empty($query->getIds())) {
            $qb
                ->andWhere($qb->expr()->in($this->alias . '.id', ':ids'))
                ->setParameter('ids', $query->getIds());
        }

        $this->getQueryBuilder()->modifyQuery($qb)->withCreatedBy();
    }
}
