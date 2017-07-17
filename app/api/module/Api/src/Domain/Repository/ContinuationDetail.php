<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ContinuationDetail
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ContinuationDetail extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch ContinuationDetail (used for markers) for a licence
     *
     * @param int $licenceId
     *
     * @return array of ContinuationDetail
     */
    public function fetchForLicence($licenceId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('licence', 'l')
            ->with('continuation', 'c');

        // where licence is
        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'))
            ->setParameter('licence', $licenceId);

        // AND licence status is 'Valid', 'Curtailed' or 'Suspended'
        $qb->andWhere($qb->expr()->in('l.status', ':licenceStatuses'))
            ->setParameter(
                'licenceStatuses',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED,
                ]
            );

        // AND continuation data is within 4 years of current date
        $dateTime = new DateTime();
        $year = $dateTime->format('Y');
        $month = $dateTime->format('n');

        $dateTime->modify('+4 years');
        $futureYear = $dateTime->format('Y');
        $futureMonth = $month;

        $pastDateTime = (new DateTime())->modify('-4 years');
        $pastYear = $pastDateTime->format('Y');
        $pastMonth = $pastDateTime->format('n');

        $qb->andWhere(
            // 4 years in the future
            '(c.month >= :month AND c.year = :year) OR '
            . '(c.year > :year AND c.year < :futureYear) OR '
            . '(c.month <= :futureMonth AND c.year = :futureYear) OR '
            // 4 years in the past
            . '(c.month <= :month AND c.year = :year) OR '
            . '(c.year > :pastYear AND c.year < :year) OR '
            . '(c.month >= :pastMonth AND c.year = :pastYear)'
        )
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->setParameter('futureMonth', $futureMonth)
            ->setParameter('futureYear', $futureYear)
            ->setParameter('pastMonth', $pastMonth)
            ->setParameter('pastYear', $pastYear);

        // AND continuation status is Printed, Acceptable or Unacceptable
        // OR where the status is Complete but the checklist has not yet been received;
        $qb->andWhere(
            "{$this->alias}.status IN (:continuationDetailStatuses) OR "
            . "({$this->alias}.status = '". Entity::STATUS_COMPLETE ."' AND {$this->alias}.received = 'N')"
        )->setParameter(
            'continuationDetailStatuses',
            [
                Entity::STATUS_PRINTED,
                Entity::STATUS_ACCEPTABLE,
                Entity::STATUS_UNACCEPTABLE
            ]
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * Get ongoing continuation detail for a licence
     *
     * @param int $licenceId Licence ID
     *
     * @return Entity
     * @throws \Doctrine\ORM\NoResultException if not found
     */
    public function fetchOngoingForLicence($licenceId)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('continuation', 'c');

        // where licence is
        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'))
            ->setParameter('licence', $licenceId);
        $qb->andWhere(
            $qb->expr()->orX(
                // and status is Acceptable
                $qb->expr()->eq($this->alias . '.status', ':status'),
                // or status is not Complete and isDigital flag = 1
                $qb->expr()->andX(
                    $qb->expr()->neq($this->alias . '.status', ':notStatus'),
                    $qb->expr()->eq($this->alias . '.isDigital', 1)
                )
            )
        )
        ->setParameter('status', Entity::STATUS_ACCEPTABLE)
        ->setParameter('notStatus', Entity::STATUS_COMPLETE);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return array
     */
    public function fetchChecklistReminders($month, $year, array $ids = [])
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb */
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata();

        $qb
            ->select(
                $this->alias .
                ', partial l.{id, licNo}' .
                ', partial lgp.{id}' .
                ', partial lo.{id, name, allowEmail}' .
                ', partial ls.{id, description}' .
                ', partial lf.{id, feeType, feeStatus}' .
                ', partial lfft.{id}' .
                ', partial lfftft.{id}' .
                ', partial lffs.{id}'
            )
            ->innerJoin($this->alias . '.continuation', 'c')
            ->innerJoin($this->alias . '.licence', 'l')
            ->leftJoin('l.status', 'ls')
            ->leftJoin('l.goodsOrPsv', 'lgp')
            ->leftJoin('l.organisation', 'lo')
            ->leftJoin('l.fees', 'lf')
            ->leftJoin('lf.feeType', 'lfft')
            ->leftJoin('lfft.feeType', 'lfftft')
            ->leftJoin('lf.feeStatus', 'lffs');

        $qb->andWhere($qb->expr()->in('l.status', ':licenceStatuses'))
            ->setParameter(
                'licenceStatuses',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED,
                ]
            );

        $qb->andWhere($qb->expr()->eq($this->alias . '.received', 0));

        if ($ids) {
            $this->getQueryBuilder()
                ->modifyQuery($qb)
                ->filterByIds($ids);
        }
        if ($month) {
            $qb->andWhere($qb->expr()->eq('c.month', ':month'))
                ->setParameter('month', $month);
        }
        if ($year) {
            $qb->andWhere($qb->expr()->eq('c.year', ':year'))
                ->setParameter('year', $year);
        }

        //  check continuation details status
        $qb->andWhere(
            $qb->expr()->neq($this->alias . '.status', ':status')
        )
        ->setParameter('status', Entity::STATUS_PREPARED);

        return $this->filterByFee(
            $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT),
            FeeTypeEntity::FEE_TYPE_CONT,
            [FeeEntity::STATUS_OUTSTANDING]
        );
    }

    /**
     * Filter by fee
     *
     * @param array $entities
     * @param string $feeType
     * @param array $feeStatuses
     * @return ArrayCollection
     */
    protected function filterByFee($entities, $feeType, $feeStatuses)
    {
        $filtered = new ArrayCollection();
        foreach ($entities as $entity) {
            $fees = $entity->getLicence()->getFees();
            if (count($fees)) {
                foreach ($fees as $fee) {
                    if ($fee->getFeeType()->getFeeType()->getId() === $feeType
                        && in_array($fee->getFeeStatus()->getId(), $feeStatuses, true) !== false
                    ) {
                        continue 2;
                    }
                }
            }
            $filtered->add($entity);
        }

        return $filtered;
    }

    public function fetchDetails($continuationId, $licenceStatuses, $licNo, $method, $status)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('continuation', 'c')
            ->with('status', 's')
            ->with('licence', 'l')
            ->with('l.status', 'ls')
            ->with('l.organisation', 'lo')
            ->with('l.licenceType', 'lt')
            ->with('l.goodsOrPsv', 'lg');

        $qb->orderBy('l.licNo', 'ASC');

        if ($continuationId) {
            $qb->andWhere($qb->expr()->eq('c.id', ':continuationId'))
                ->setParameter('continuationId', $continuationId);
        }
        if ($licenceStatuses) {
            $qb->andWhere($qb->expr()->in('l.status', ':licenceStatuses'))
                ->setParameter('licenceStatuses', $licenceStatuses);
        }
        if ($licNo) {
            $qb->andWhere($qb->expr()->eq('l.licNo', ':licNo'))
                ->setParameter('licNo', $licNo);
        }
        if ($method) {
            if ($method === Entity::METHOD_EMAIL) {
                $qb->andWhere($qb->expr()->eq('lo.allowEmail', 1));
            } elseif ($method === Entity::METHOD_POST) {
                $qb->andWhere($qb->expr()->eq('lo.allowEmail', 0));
            }
        }
        if ($status) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.status', ':status'))
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    public function fetchWithLicence($id)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('status', 's')
            ->with('licence', 'l')
            ->with('l.licenceType', 'lt')
            ->with('l.goodsOrPsv', 'lg')
            ->byId($id);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Fetch licence ids where continuation details exists for given continuation
     *
     * @param int $continuationId
     * @param array $licenceIds
     *
     * @return array
     */
    public function fetchLicenceIdsForContinuationAndLicences($continuationId, $licenceIds)
    {
        /* @var $qb \Doctrine\Orm\QueryBuilder */
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('licence', 'l')
            ->withRefdata();

        // where licence is
        $qb->andWhere($qb->expr()->in($this->alias . '.licence', ':licences'))
            ->setParameter('licences', $licenceIds);
        // and continunation is
        $qb->andWhere($qb->expr()->eq($this->alias . '.continuation', ':continuation'))
            ->setParameter('continuation', $continuationId);

        $result = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $licenceIds = [];

        foreach ($result as $res) {
            $licenceIds[] = $res['licence']['id'];
        }

        return $licenceIds;
    }

    /**
     * Create continuation details
     *
     * @param array $licenceIds
     * @param bool $received
     * @param string $status
     * @param int $continuationId
     *
     * @return int
     */
    public function createContinuationDetails($licenceIds, $received, $status, $continuationId)
    {
        return $this->getDbQueryManager()->get('Continuations\CreateContinuationDetails')
            ->executeInsert($licenceIds, $received, $status, $continuationId);
    }
}
