<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as Entity;
use DateTime;

/**
 * IRHP Permit Window
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitWindow extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Returns an array of IrhpPermitWindow objects that are open as of the specified date and time
     *
     * @param DateTime $currentDateTime
     *
     * @return array
     */
    public function fetchOpenWindows(DateTime $currentDateTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('ipw')
            ->from(Entity::class, 'ipw')
            ->add(
                'where',
                $qb->expr()->between('?1', 'ipw.startDate', 'ipw.endDate')
            )
            ->setParameter(1, $currentDateTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns the IrhpPermitWindow that was most recently open prior to the specified date and time, or null if there
     * were no windows open prior to the specified date
     *
     * @param DateTime $currentDateTime
     *
     * @return Entity|null
     */
    public function fetchLastOpenWindow(DateTime $currentDateTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('ipw')
            ->from(Entity::class, 'ipw')
            ->add(
                'where',
                $qb->expr()->lt('ipw.endDate', '?1')
            )
            ->orderBy('ipw.endDate', 'DESC')
            ->setParameter(1, $currentDateTime)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
