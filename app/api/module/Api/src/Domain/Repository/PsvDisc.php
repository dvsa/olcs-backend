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
        $qb->setParameter('licenceType', $licenceType);
        $qb->setParameter('licenceTrafficAreaId', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
        $qb->setParameter('goodsOrPsv', LicenceEntity::LICENCE_CATEGORY_PSV);
    }
}
