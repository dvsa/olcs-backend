<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as Entity;

/**
 * SeriousInfringement
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SeriousInfringement extends AbstractRepository
{
    protected $entity = Entity::class;

    protected function applyListFilters(\Doctrine\ORM\QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (method_exists($query, 'getCase') && !empty($query->getCase())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':case'))
                ->setParameter('case', $query->getCase());
        }

        parent::applyListFilters($qb, $query);
    }

    /**
     * Get a serious infringement by the notification number
     *
     * @param string $notificationNumber
     *
     * @return Entity|null
     */
    public function fetchByNotificationNumber($notificationNumber)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb);

        $dqb->where($dqb->expr()->eq($this->alias .'.notificationNumber', ':notificationNumber'))
            ->setParameter('notificationNumber', $notificationNumber)
            ->setMaxResults(1);

        $results = $dqb->getQuery()->getResult();

        if (!empty($results)) {
            return $results[0];
        }

        return null;
    }
}
