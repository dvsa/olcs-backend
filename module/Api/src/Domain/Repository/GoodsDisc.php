<?php

/**
 * Goods Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc as Entity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TraficArea as TrafficAreaEntity;

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

    public function fetchDiscsToPrint($niFlag, $operatorType, $licenceType)
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
            ->with('lv.application','lva')
            ->with('lva.licenceType', 'lvalt');

        $this->addFilteringConditions($qb, $niFlag, $licenceType, $operatorType);

        return $qb->getQuery()->getResult();
    }

    protected function addFilteringConditions($qb, $niFlag, $licenceType, $operatorType)
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
                    $qb->andX(
                        $qb->expr()->eq('lvlta.isNi', 1),
                        $qb->expr()->eq($this->alias. '.isInterim', 0),
                        $qb->expr()->eq('lvllt.id', ':licenceLicenceType'),
                        $qb->expr()->eq('lvlta.id', ':licenceTrafficAreaId1')
                    )
                )
            );
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
                        $qb->expr()->eq('lvlgp.id', ':operatorType'),
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
            $qb->setParameter('operatorType', $operatorType);
            $qb->setParameter('applicationLicenceType', $licenceType);
            $qb->setParameter('licenceTrafficAreaId', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);

            $qb->setParameter('operatorType1', $operatorType);
            $qb->setParameter('licenceLicenceType', $licenceType);
            $qb->setParameter('licenceTrafficAreaId1', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
        }
    }
}
