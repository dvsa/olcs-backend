<?php

/**
 * ContinuationDetail
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;

/**
 * ContinuationDetail
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
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
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
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
                    LicenceEntity::LICENCE_STATUS_SUSPENDED
                ]
            );

        // AND continuation data is within 4 years of current date
        $dateTime = new DateTime();
        $year = $dateTime->format('Y');
        $month = $dateTime->format('n');

        $dateTime->modify('+4 years');
        $futureYear = $dateTime->format('Y');
        $futureMonth = $month;

        $qb->andWhere(
            '(c.month >= :month AND c.year = :year) OR '
            . '(c.year > :year AND c.year < :futureYear) OR '
            . '(c.month <= :futureMonth AND c.year = :futureYear)'
        )
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->setParameter('futureMonth', $futureMonth)
            ->setParameter('futureYear', $futureYear);

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
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('continuation', 'c');

        // where licence is
        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'))
            ->setParameter('licence', $licenceId);
        // and status is Acceptable
        $qb->andWhere($qb->expr()->eq($this->alias . '.status', ':status'))
            ->setParameter('status', Entity::STATUS_ACCEPTABLE);

        return $qb->getQuery()->getSingleResult();
    }

    public function fetchChecklistReminders($month, $year, $ids = [])
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('continuation', 'c')
            ->with('licence', 'l')
            ->with('l.status', 'ls')
            ->with('l.licenceType', 'lt')
            ->with('l.goodsOrPsv', 'lgp')
            ->with('l.organisation', 'lo')
            ->with('l.fees', 'lf')
            ->with('lf.feeType', 'lfft');

        $qb->andWhere($qb->expr()->in('l.status', ':licenceStatuses'))
            ->setParameter(
                'licenceStatuses',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED
                ]
            );

        $qb->andWhere($qb->expr()->eq($this->alias . '.received', 0));

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->not(
                    $qb->expr()->andX(
                        $qb->expr()->eq('lfft.feeType', ':feeType'),
                        $qb->expr()->in('lf.feeStatus', ':feeStatus')
                    )
                ),
                $qb->expr()->isNull('lf.id')
            )
        );
        $qb->setParameter('feeType', FeeTypeEntity::FEE_TYPE_CONT);
        $qb->setParameter('feeStatus', [FeeEntity::STATUS_OUTSTANDING, FeeEntity::STATUS_WAIVE_RECOMMENDED]);

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

        return $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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

        return $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
