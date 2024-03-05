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

    public function getUnreadConversationCountByLicenceIdAndRoles(int $licenceId, array $roleNames): int
    {
        $qb = $this->createQueryBuilder();

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery
            ->select('1')
            ->from(\Dvsa\Olcs\Api\Entity\Messaging\MessagingUserMessageRead::class, 'inner_read')
            ->leftJoin('inner_read.user', 'inner_user')
            ->leftJoin('inner_user.roles', 'inner_role')
            ->where('inner_read.messagingMessage = ' . $this->alias . '.id')
            ->andWhere($qb->expr()->in('inner_role.role', ':roleNames'));

        $qb
            ->leftJoin($this->alias . '.messagingConversation', 'inner_conversation')
            ->leftJoin('inner_conversation.task', 'inner_task')
            ->groupBy($this->alias . '.messagingConversation')
            ->where('inner_conversation.isClosed = 0')
            ->andWhere('inner_task.licence = :licenceId')
            ->andWhere(
                $qb->expr()->not(
                    $qb->expr()->exists($subQuery->getDQL())
                )
            );

        $qb->setParameter('licenceId', $licenceId);
        $qb->setParameter('roleNames', $roleNames);

        return count($qb->getQuery()->getScalarResult());
    }

    public function getUnreadConversationCountByOrganisationIdAndUserId(int $organisationId, int $userId): int
    {
        $qb = $this->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->leftJoin($this->alias . '.userMessageReads', 'umr', 'WITH', 'umr.user = :userId')
            ->leftJoin($this->alias . '.messagingConversation', 'c')
            ->leftJoin('c.task', 't')
            ->leftJoin('t.licence', 'l')
            ->leftJoin('l.organisation', 'o')
            ->andWhere('o.id = :organisationId')
            ->andWhere('umr.id IS NULL')
            ->groupBy($this->alias . '.messagingConversation')
            ->setParameter('organisationId', $organisationId)
            ->setParameter('userId', $userId);

        return count($qb->getQuery()->getScalarResult());
    }
}
