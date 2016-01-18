<?php

/**
 * Goods Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Doctrine\ORM\Query;

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

        $qb->leftJoin('gd.licenceVehicle', 'lv')
            ->leftJoin('lv.licence', 'lvl')
            ->leftJoin('lvl.goodsOrPsv', 'lvlgp')
            ->leftJoin('lvl.licenceType', 'lvllt')
            ->leftJoin('lvl.trafficArea', 'lvlta')
            ->leftJoin('lv.vehicle', 'lvv')
            ->leftJoin('lv.application', 'lva')
            ->leftJoin('lva.licenceType', 'lvalt')
            ->leftJoin('lva.goodsOrPsv', 'lvagp');

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
                        $qb->expr()->eq('lvalt.id', ':applicationLicenceType')
                    ),
                    // isInterm = 0
                    $qb->expr()->andX(
                        $qb->expr()->eq('lvlta.isNi', 1),
                        $qb->expr()->eq($this->alias. '.isInterim', 0),
                        $qb->expr()->eq('lvllt.id', ':licenceLicenceType')
                    )
                )
            );
            $qb->andWhere($qb->expr()->isNull('gd.issuedDate'));
            $qb->andWhere($qb->expr()->isNull('gd.ceasedDate'));

            $qb->setParameter('applicationLicenceType', $licenceType);

            $qb->setParameter('licenceLicenceType', $licenceType);
        } else {
            // for non-NI licences we should check operator type as well
            $qb->andWhere(
                $qb->expr()->orX(
                    //isInterm = 1
                    $qb->expr()->andX(
                        // need to pick up discs from all traffic areas apart from NI
                        $qb->expr()->eq('lvlta.isNi', 0),
                        $qb->expr()->eq($this->alias. '.isInterim', 1),
                        $qb->expr()->eq('lvagp.id', ':operatorType'),
                        $qb->expr()->eq('lvalt.id', ':applicationLicenceType')
                    ),
                    //isInterm = 0
                    $qb->expr()->andX(
                        // need to pick up discs from all traffic areas apart from NI
                        $qb->expr()->eq('lvlta.isNi', 0),
                        $qb->expr()->eq($this->alias. '.isInterim', 0),
                        $qb->expr()->eq('lvlgp.id', ':operatorType1'),
                        $qb->expr()->eq('lvllt.id', ':licenceLicenceType')
                    )
                )
            );
            $qb->andWhere($qb->expr()->isNull('gd.issuedDate'));
            $qb->andWhere($qb->expr()->isNull('gd.ceasedDate'));

            $qb->setParameter('operatorType', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
            $qb->setParameter('applicationLicenceType', $licenceType);

            $qb->setParameter('operatorType1', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
            $qb->setParameter('licenceLicenceType', $licenceType);

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

    public function ceaseDiscsForLicence($licenceId)
    {
        return $this->getDbQueryManager()->get('ceaseDiscsForLicence')->execute(['licence' => $licenceId]);
    }
}
