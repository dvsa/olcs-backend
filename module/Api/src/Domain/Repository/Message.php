<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class Message extends AbstractRepository
{
    protected $entity = Entity::class;

    public function getBaseMessageListQuery(QueryInterface $query): QueryBuilder
    {
        $qb = $this->createDefaultListQuery($query);

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('createdBy')
            ->with('lastModifiedBy')
            ->order('createdOn', 'desc');

        return $qb;
    }

    public function getBaseMessageListWithContentQuery(QueryInterface $query): QueryBuilder
    {
        $qb = $this->getBaseMessageListQuery($query);

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('messagingContent')
            ->with('documents')
            ->withCreatedByWithTeam();

        return $qb;
    }

    public function filterByConversationId(QueryBuilder $qb, $conversationId): QueryBuilder
    {
        $qb
            ->andWhere($qb->expr()->eq($this->alias . '.messagingConversation', ':messagingConversation'))
            ->setParameter('messagingConversation', $conversationId);

        return $qb;
    }

    public function getLastMessageByConversationId($conversationId): array
    {
        $qb = $this->createQueryBuilder();
        $qb = $this->filterByConversationId($qb, $conversationId);
        $qb->orderBy($this->alias . '.id', 'desc');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
    }

    public function getUnreadMessagesByConversationIdAndUserId($conversationId, $userId): array
    {
        $qb = $this->createQueryBuilder()
            ->leftJoin($this->alias . '.userMessageReads', 'umr', 'WITH', 'umr.user = :userId')
            ->andWhere($this->alias . '.messagingConversation = :conversationId')
            ->andWhere('umr.id IS NULL')
            ->setParameter('conversationId', $conversationId)
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
