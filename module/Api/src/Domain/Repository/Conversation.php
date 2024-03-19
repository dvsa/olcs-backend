<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation as Entity;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage;
use Dvsa\Olcs\Api\Entity\User\Role;
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

    public function applyOrderForListing(QueryBuilder $qb, array $roleNames): QueryBuilder
    {
        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('1')
                 ->from(\Dvsa\Olcs\Api\Entity\Messaging\MessagingUserMessageRead::class, 'inner_read')
                 ->leftJoin('inner_read.user', 'inner_user')
                 ->leftJoin('inner_user.roles', 'inner_role')
                 ->where('inner_read.messagingMessage = irmm.id')
                 ->andWhere($qb->expr()->in('inner_role.role', ':roleNames'));

        $unreadQueryBuilder = $this->getEntityManager()->createQueryBuilder();
        $unreadQueryBuilder->select('1')
            ->from(MessagingMessage::class, 'irmm')
            ->leftJoin('irmm.messagingConversation', 'inner_conversation')
            ->leftJoin('inner_conversation.task', 'inner_task')
            ->groupBy('irmm.messagingConversation')
            ->where('inner_conversation.isClosed = 0')
            ->andWhere($unreadQueryBuilder->expr()->not($unreadQueryBuilder->expr()->exists($subQuery)))
            ->andWhere($unreadQueryBuilder->expr()->eq($this->alias . '.id', 'irmm.messagingConversation'));

        $lastReadQuery = $this->getEntityManager()->createQueryBuilder();
        $lastReadQuery->select('MAX(lrmm.createdOn)')
                      ->from(MessagingMessage::class, 'lrmm')
                      ->where($lastReadQuery->expr()->eq('lrmm.messagingConversation', $this->alias . '.id'))
                      ->groupBy('lrmm.messagingConversation');

        $qb->addSelect('(' . $lastReadQuery->getDQL() . ') AS last_read');
        $qb->addSelect('(' . $unreadQueryBuilder->getDQL() . ') AS has_unread');
        $qb->addOrderBy($this->alias . '.isClosed', 'ASC');
        $qb->addOrderBy('has_unread', 'DESC');
        $qb->addOrderBy('last_read', 'DESC');

        $qb->setParameter('roleNames', $roleNames);

        return $qb;
    }
}
