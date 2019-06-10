<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use DateTime;
use Doctrine\ORM\NoResultException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath as Entity;

/**
 * Application Path
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationPath extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Return the application path corresponding to the specified permit type and date
     *
     * @param int $irhpPermitTypeId
     * @param DateTime $now
     *
     * @return Entity
     *
     * @throws NotFoundException
     */
    public function fetchByIrhpPermitTypeIdAndDate($irhpPermitTypeId, DateTime $now)
    {
        try {
            $qb = $this->getEntityManager()->createQueryBuilder();

            return $qb->select('ap')
                ->from(Entity::class, 'ap')
                ->where('IDENTITY(ap.irhpPermitType) = :type')
                ->andWhere($qb->expr()->lte('ap.effectiveFrom', ':now'))
                ->orderBy('ap.effectiveFrom', 'DESC')
                ->setParameter('type', $irhpPermitTypeId)
                ->setParameter('now', $now)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            throw new NotFoundException('Unable to locate application path');
        }
    }
}
