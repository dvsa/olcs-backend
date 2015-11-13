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
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

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

        return $qb->getQuery()->getResult();
    }

    protected function addFilteringConditions($qb, $licenceType)
    {
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
    }

    public function setIsPrintingOn($discs)
    {
        $this->setIsPrinting('Y', $discs);
    }

    public function setIsPrintingOff($discs)
    {
        $this->setIsPrinting('N', $discs);
    }

    protected function setIsPrinting($type, $discs)
    {
        foreach ($discs as $disc) {
            $fetched = $this->fetchById($disc->getId());
            $fetched->setIsPrinting($type);
            $this->save($fetched);
        }
    }

    public function setIsPrintingOffAndAssignNumbers($discs, $startNumber)
    {
        foreach ($discs as $disc) {
            $fetched = $this->fetchById($disc->getId());
            $fetched->setIsPrinting('N');
            $fetched->setDiscNo($startNumber++);
            $fetched->setIssuedDate(new DateTime('now'));
            $this->save($fetched);
        }
    }

    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (method_exists($query, 'getIncludeCeased')) {
            if ($query->getIncludeCeased() === false) {
                $qb->andWhere($qb->expr()->isNull($this->alias . '.ceasedDate'));
            }
        }

        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'))
            ->setParameter('licence', $query->getId());
    }
}
