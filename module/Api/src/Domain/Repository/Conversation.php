<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class Conversation extends AbstractRepository
{
    protected $entity = Entity::class;

    public function getBaseConversationListQuery(QueryInterface $query): QueryBuilder
    {
        $qb = $this->createDefaultListQuery($query);

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('task', 't1')
            ->with('t1.licence', 'l1')
            ->with('t1.application')
            ->with('t1.category')
            ->with('t1.subCategory')
            ->with('createdBy')
            ->with('lastModifiedBy');
        return $qb;
    }

    public function filterByLicenceId(QueryBuilder $qb, $licenceId): QueryBuilder
    {
        $qb
            ->innerJoin($this->alias . '.task', 't')
            ->andWhere($qb->expr()->isNotNull('t.licence'))
            ->andWhere($qb->expr()->eq('t.licence', ':licence'))
            ->setParameter('licence', $licenceId);

        return $qb;
    }

    public function getByOrganisationId(QueryInterface $query, int $organisationId): QueryBuilder
    {
        $qb = $this->getBaseConversationListQuery($query);

        $qb->innerJoin($this->alias . '.task', 't')
            ->andWhere($qb->expr()->isNotNull('t.licence'))
            ->andWhere($qb->expr()->eq('l1.organisation', ':organisation'))
            ->setParameter('organisation', $organisationId)
            ->addOrderBy($this->alias . '.isClosed', 'ASC');

        return $qb;
    }

    public function filterByApplicationId(QueryBuilder $qb, $applicationId): QueryBuilder
    {
        $qb
            ->innerJoin($this->alias . '.task', 't')
            ->andWhere($qb->expr()->isNotNull('t.application'))
            ->andWhere($qb->expr()->eq('t.application', ':application'))
            ->setParameter('application', $applicationId);

        return $qb;
    }

    public function applyOrderByOpen(QueryBuilder $qb): QueryBuilder
    {
        $qb->addOrderBy($this->alias . '.isClosed', 'ASC');

        return $qb;
    }

    public function filterByStatuses(QueryBuilder $qb, array $statuses): QueryBuilder
    {
        $conditions = [];

        if (in_array('open', $statuses)) {
            $conditions[] = $this->alias . '.isClosed = 0';
        }

        if (in_array('closed', $statuses)) {
            $conditions[] = $this->alias . '.isClosed = 1';
        }

        if (count($conditions) > 0) {
            $qb->andWhere($qb->expr()->orX()->addMultiple($conditions));
        }

        return $qb;
    }
}
