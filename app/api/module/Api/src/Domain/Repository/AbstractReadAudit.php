<?php

/**
 * Abstract Read Audit
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;
use Doctrine\ORM\Query;

/**
 * Abstract Read Audit
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReadAudit extends AbstractRepository
{
    protected $entityProperty = null;

    public function fetchOne($userId, $entityId, $date)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias . '.user', ':user'));
        $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->entityProperty, ':entityId'));
        $qb->andWhere($qb->expr()->eq($this->alias . '.createdOn', ':date'));

        $qb->setParameter(':user', $userId);
        $qb->setParameter(':entityId', $entityId);
        $qb->setParameter(':date', $date);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
