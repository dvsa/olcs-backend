<?php

/**
 * Goods Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Goods Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsDisc extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'gd';

    public function fetchDiscsToPrint($niFlag, $licenceType)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('licenceVehicle', 'lv')
            ->with('lv.licence', 'lvl')
            ->with('lvl.goodsOrPsv', 'lvlgp')
            ->with('lvl.licenceType', 'lvllt')
            ->with('lvl.trafficArea', 'lvlta')
            ->with('lv.vehicle', 'lvv')
            ->with('lv.application', 'lva')
            ->with('lva.licenceType', 'lvalt')
            ->with('lva.goodsOrPsv', 'lvagp');

        $this->addFilteringConditions($qb, $niFlag, $licenceType);

        return $qb->getQuery()->getResult();
    }

    protected function addFilteringConditions($qb, $niFlag, $licenceType)
    {
        if ($niFlag == 'Y') {
            // for NI licences we don't check operator type
            $qb->andWhere(
                $qb->expr()->orX(
                    //isInterm = 1
                    $qb->expr()->andX(
                        $qb->expr()->eq('lvlta.isNi', 1),
                        $qb->expr()->eq($this->alias. '.isInterim', 1),
                        $qb->expr()->eq('lvalt.id', ':applicationLicenceType'),
                        $qb->expr()->eq('lvlta.id', ':licenceTrafficAreaId')
                    ),
                    // isInterm = 0
                    $qb->expr()->andX(
                        $qb->expr()->eq('lvlta.isNi', 1),
                        $qb->expr()->eq($this->alias. '.isInterim', 0),
                        $qb->expr()->eq('lvllt.id', ':licenceLicenceType'),
                        $qb->expr()->eq('lvlta.id', ':licenceTrafficAreaId1')
                    )
                )
            );
            $qb->andWhere($qb->expr()->isNull('gd.issuedDate'));
            $qb->andWhere($qb->expr()->isNull('gd.ceasedDate'));

            $qb->setParameter('applicationLicenceType', $licenceType);
            $qb->setParameter('licenceTrafficAreaId', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);

            $qb->setParameter('licenceLicenceType', $licenceType);
            $qb->setParameter('licenceTrafficAreaId1', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
        } else {
            // for non-NI licences we should check operator type as well
            $qb->andWhere(
                $qb->expr()->orX(
                    //isInterm = 1
                    $qb->expr()->andX(
                        $qb->expr()->eq('lvlta.isNi', 0),
                        $qb->expr()->eq($this->alias. '.isInterim', 1),
                        $qb->expr()->eq('lvagp.id', ':operatorType'),
                        $qb->expr()->eq('lvalt.id', ':applicationLicenceType'),
                        // need to pick up discs from all traffic areas apart from NI
                        $qb->expr()->neq('lvlta.id', ':licenceTrafficAreaId')
                    ),
                    //isInterm = 0
                    $qb->expr()->andX(
                        $qb->expr()->eq('lvlta.isNi', 0),
                        $qb->expr()->eq($this->alias. '.isInterim', 0),
                        $qb->expr()->eq('lvlgp.id', ':operatorType1'),
                        $qb->expr()->eq('lvllt.id', ':licenceLicenceType'),
                        // need to pick up discs from all traffic areas apart from NI
                        $qb->expr()->neq('lvlta.id', ':licenceTrafficAreaId1')
                    )
                )
            );
            $qb->andWhere($qb->expr()->isNull('gd.issuedDate'));
            $qb->andWhere($qb->expr()->isNull('gd.ceasedDate'));

            $qb->setParameter('operatorType', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
            $qb->setParameter('applicationLicenceType', $licenceType);
            $qb->setParameter('licenceTrafficAreaId', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);

            $qb->setParameter('operatorType1', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
            $qb->setParameter('licenceLicenceType', $licenceType);
            $qb->setParameter('licenceTrafficAreaId1', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);

        }
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
}
