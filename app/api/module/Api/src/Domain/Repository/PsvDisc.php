<?php

/**
 * Psv Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Doctrine\DBAL\Connection;

/**
 * Psv Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PsvDisc extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'psv';

    public function fetchDiscsToPrint($licenceType)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('licence', 'l')
            ->with('l.trafficArea', 'lta')
            ->with('l.licenceType', 'llt')
            ->with('l.goodsOrPsv', 'lgp');

        $this->addFilteringConditions($qb, $licenceType);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    protected function addFilteringConditions($qb, $licenceType)
    {
        $activeStatuses = [
            LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
            LicenceEntity::LICENCE_STATUS_GRANTED,
            LicenceEntity::LICENCE_STATUS_VALID,
            LicenceEntity::LICENCE_STATUS_CURTAILED,
            LicenceEntity::LICENCE_STATUS_SUSPENDED
        ];
        $qb->andWhere(
            $qb->expr()->andX(
                $qb->expr()->eq('lta.isNi', 0),
                $qb->expr()->eq('llt.id', ':licenceType'),
                $qb->expr()->neq('lta.id', ':licenceTrafficAreaId'),
                $qb->expr()->eq('lgp.id', ':goodsOrPsv')
            )
        );
        $qb->andWhere($qb->expr()->isNull('psv.issuedDate'));
        $qb->andWhere($qb->expr()->isNull('psv.ceasedDate'));
        $qb->setParameter('licenceType', $licenceType);
        $qb->setParameter('licenceTrafficAreaId', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
        $qb->setParameter('goodsOrPsv', LicenceEntity::LICENCE_CATEGORY_PSV);

        $qb->andWhere($qb->expr()->in('l.status', ':activeStatuses'));
        $qb->setParameter('activeStatuses', $activeStatuses);
    }

    public function setIsPrintingOn($discIds)
    {
        $this->setIsPrinting(1, $discIds);
    }

    public function setIsPrintingOff($discIds)
    {
        $this->setIsPrinting(0, $discIds);
    }

    protected function setIsPrinting($type, $discIds)
    {
        return $this->getDbQueryManager()->get('Discs\PsvDiscsSetIsPrinting')
            ->execute(
                ['isPrinting' => $type, 'ids' => $discIds],
                ['isPrinting' => \PDO::PARAM_INT, 'ids' => Connection::PARAM_INT_ARRAY]
            );
    }

    public function setIsPrintingOffAndAssignNumbers($discIds, $startNumber)
    {
        return $this->getDbQueryManager()->get('Discs\PsvDiscsSetIsPrintingOffAndDiscNo')
            ->execute(
                ['ids' => $discIds, 'startNumber' => $startNumber],
                ['ids' => Connection::PARAM_INT_ARRAY, 'startNumber' => \PDO::PARAM_INT]
            );
    }

    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (method_exists($query, 'getIncludeCeased')) {
            if ($query->getIncludeCeased() === false) {
                $qb->andWhere($qb->expr()->isNull($this->alias . '.ceasedDate'));
            }
        }

        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'))
            ->setParameter('licence', $query->getId())
            ->addSelect($this->alias . '.discNo+0 as HIDDEN intDiscNo')
            ->orderBy('intDiscNo', 'ASC');
    }

    public function ceaseDiscsForLicence($licenceId)
    {
        return $this->getDbQueryManager()->get('Discs\CeaseDiscsForLicence')
            ->execute(['licence' => $licenceId]);
    }

    public function fetchDiscsToPrintMin($licenceType)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('psv.licence', 'l')
            ->leftJoin('l.trafficArea', 'lta')
            ->leftJoin('l.licenceType', 'llt')
            ->leftJoin('l.goodsOrPsv', 'lgp');

        $this->addFilteringConditions($qb, $licenceType);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Create PSV discs
     *
     * @param int  $licenceId
     * @param int  $howMany   How many to create
     * @param bool $isCopy    Set created discs as copies
     *
     * @return int number of discs created
     */
    public function createPsvDiscs($licenceId, $howMany, $isCopy = false)
    {
        return $this->getDbQueryManager()->get('Discs\CreatePsvDiscs')
            ->executeInsert($licenceId, $howMany, $isCopy);
    }
}
