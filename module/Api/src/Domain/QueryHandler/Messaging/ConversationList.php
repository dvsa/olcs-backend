<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\GetConversationList as GetConversationListQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ConversationList extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    private const STATUS_CLOSED = "CLOSED";
    private const STATUS_NEW_MESSAGE = "NEW_MESSAGE";
    private const STATUS_OPEN = "OPEN";

    protected $extraRepos = ['Conversation', 'Message'];

    public function handleQuery(QueryInterface $query)
    {
        assert($query instanceof GetConversationListQuery);
        $conversationRepository = $this->getRepo('Conversation');
        assert($conversationRepository instanceof ConversationRepo);

        $conversationsQuery = $conversationRepository->getBaseConversationListQuery($query);
        if (!empty($query->getLicence())) {
            $conversationsQuery = $conversationRepository->filterByLicenceId($conversationsQuery, $query->getLicence());
        }

        if (!empty($query->getApplication())) {
            $conversationsQuery = $conversationRepository->filterByApplicationId($conversationsQuery, $query->getApplication());
        }

        if ($query->getApplyOpenMessageSorting()) {
            $conversationsQuery = $conversationRepository->applyOrderByOpen($conversationsQuery);
        }

        $conversations = $conversationRepository->fetchPaginatedList($conversationsQuery);
        foreach ($conversations as $key => $value) {
            $unreadMessageCount = $this->getUnreadMessageCountForUser($value);
            $conversations[$key]['userContextUnreadCount'] = $unreadMessageCount;
            $conversations[$key]['userContextStatus'] = $this->stringifyMessageStatusForUser($value, $unreadMessageCount);
            $conversations[$key]['latestMessage'] = $this->getLatestMessageMetadata($value['id']);
        }

        if ($query->getApplyNewMessageSorting()) {
            $conversations = $this->orderResultPrioritisingNewMessages($conversations);
        }

        return [
            'result' => $conversations,
            'count' => $conversationRepository->fetchPaginatedCount($conversationsQuery),
        ];
    }

    private function stringifyMessageStatusForUser($conversation, $count): string
    {
        if ($conversation['isClosed']) {
            return self::STATUS_CLOSED;
        }
        if ($count > 0) {
            return self::STATUS_NEW_MESSAGE;
        }
        return self::STATUS_OPEN;
    }

    private function getUnreadMessageCountForUser($conversation): int
    {
        $messageRepository = $this->getRepo('Message');
        assert($messageRepository instanceof MessageRepo);
        $results = $messageRepository->getUnreadMessagesByConversationIdAndUserId($conversation['id'], $this->getUser()->getId());
        return count($results);
    }

    private function getLatestMessageMetadata($conversationId): array
    {
        $messageRepository = $this->getRepo('Message');
        assert($messageRepository instanceof MessageRepo);
        return $messageRepository->getLastMessageByConversationId($conversationId);
    }

    private function orderResultPrioritisingNewMessages($conversationList): array
    {
        $conversationList = iterator_to_array($conversationList);
        usort($conversationList, function ($a, $b) {
            $aHasNewMessage = self::STATUS_NEW_MESSAGE === $a['userContextStatus'];
            $bHasNewMessage = self::STATUS_NEW_MESSAGE === $b['userContextStatus'];

            if ($aHasNewMessage && !$bHasNewMessage) {
                return -1;      // $a comes first because it has a new message
            } elseif (!$aHasNewMessage && $bHasNewMessage) {
                return 1;       // $b comes first because it has a new message
            } else {
                // If both are equal, compare by latest message creation time
                $aLatestMessageTimestamp = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $a['latestMessage']['createdOn']);
                $bLatestMessageTimestamp = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $b['latestMessage']['createdOn']);
                if ($aLatestMessageTimestamp == $bLatestMessageTimestamp) {
                    return 0;   // Equal
                }
                return ($aLatestMessageTimestamp > $bLatestMessageTimestamp) ? -1 : 1;
            }
        });

        return $conversationList;
    }
}
