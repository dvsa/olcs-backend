<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\System\PublicHoliday as Entity;

/**
 * PublicHoliday
 */
class PublicHoliday extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'p';

    /**
     * Returns an array of dates between $startDate and $endDate
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    public function fetchBetweenByTa(\DateTime $startDate, \DateTime $endDate, TrafficAreaEntity $trafficArea = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p.publicHolidayDate')->from($this->entity, $this->alias);

        $qb->where($qb->expr()->between('p.publicHolidayDate', ':start', ':end'));

        $qb->setParameter('start', $startDate, Type::DATE);
        $qb->setParameter('end', $endDate, Type::DATE);

        if ($trafficArea !== null) {
            switch (true) {
                case $trafficArea->getIsEngland():
                    $qb->andWhere($qb->expr()->eq('p.isEngland', 1));
                    break;
                case $trafficArea->getIsNi():
                    $qb->andWhere($qb->expr()->eq('p.isNi', 1));
                    break;
                case $trafficArea->getIsScotland():
                    $qb->andWhere($qb->expr()->eq('p.isScotland', 1));
                    break;
                case $trafficArea->getIsWales():
                    $qb->andWhere($qb->expr()->eq('p.isWales', 1));
                    break;
            }
        }

        return $qb->getQuery()->getArrayResult();
    }
}
