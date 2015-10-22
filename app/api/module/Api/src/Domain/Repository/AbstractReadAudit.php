<?php

/**
 * Abstract Read Audit
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

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

        $qb->setParameter('user', $userId);
        $qb->setParameter('entityId', $entityId);
        $qb->setParameter('date', $date);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Override to add additional data to the default fetchList() method
     * @param QueryBuilder $qb
     * @inheritdoc
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $qb->innerJoin($this->alias . '.user', 'u');
        $qb->innerJoin('u.contactDetails', 'cd');
        $qb->innerJoin('cd.person', 'p');
    }

    /**
     * Applies filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->entityProperty, ':byEntity'))
            ->setParameter('byEntity', $query->getId())
            ->orderBy($this->alias . '.createdOn', 'DESC');
    }
}
