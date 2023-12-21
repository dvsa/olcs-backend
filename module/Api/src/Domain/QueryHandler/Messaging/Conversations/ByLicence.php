<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByLicence as GetConversationsByLicenceQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByLicence extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    private const STATUS_CLOSED = "CLOSED";
    private const STATUS_NEW_MESSAGE = "NEW_MESSAGE";
    private const STATUS_OPEN = "OPEN";

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = ['Conversation', 'Message'];

    public function handleQuery(QueryInterface $query)
    {
        assert($query instanceof GetConversationsByLicenceQuery);
        $conversationRepository = $this->getRepository();

        $conversationsQuery = $conversationRepository->getBaseConversationListQuery($query);
        $conversationsQuery = $conversationRepository->filterByLicenceId($conversationsQuery, $query->getLicence());
        $conversationsQuery = $conversationRepository->applyOrderByOpen($conversationsQuery);
        $conversations = $conversationRepository->fetchPaginatedList($conversationsQuery);
        foreach ($conversations as $key => $value) {
            $unreadMessageCount = $this->getUnreadMessageCountForUser($value);
            $conversations[$key]['userContextUnreadCount'] = $unreadMessageCount;
            $conversations[$key]['userContextStatus'] = $this->stringifyMessageStatusForUser($value, $unreadMessageCount);
            $conversations[$key]['latestMessage'] = $this->getLatestMessageMetadata($value['id']);
        }

        $conversations = $this->orderResultPrioritisingNewMessages($conversations);

        return [
            'result' => $conversations,
            'count' => $conversationRepository->fetchPaginatedCount($conversationsQuery),
        ];
    }

    private function getUnreadMessageCountForUser($conversation): int
    {
        $messageRepository = $this->getRepo('Message');
        assert($messageRepository instanceof MessageRepo);
        $results = $messageRepository->getUnreadMessagesByConversationIdAndUserId($conversation['id'], $this->getUser()->getId());
        return count($results);
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

    private function getLatestMessageMetadata($conversationId): array
    {
        $messageRepository = $this->getRepo('Message');
        assert($messageRepository instanceof MessageRepo);
        return $messageRepository->getLastMessageByConversationId($conversationId);
    }

    /**
     * This method takes a conversation list, and returns sorted based on the following rules:
     *
     *  - Sort by conversation status in order (NEW_MESSAGE, OPEN, CLOSED)
     *  - Within the status groups, sort by latest message creation timestamp descending (newest first).
     *
     * @param $conversationList
     * @return array
     */
    private function orderResultPrioritisingNewMessages($conversationList): array
    {
        $conversationList = iterator_to_array($conversationList);

        // If we don't have 2 or more items, there is nothing to sort...
        if (count($conversationList) < 2) {
            return $conversationList;
        }

        $order = [self::STATUS_NEW_MESSAGE, self::STATUS_OPEN, self::STATUS_CLOSED];

        // Separate the data into groups based on 'userContextStatus'
        $statusGroups = array_fill_keys($order, array());
        foreach ($conversationList as $item) {
            $status = $item['userContextStatus'];
            $statusGroups[$status][] = $item;
        }

        // Sort each group by latest message created on timestamp (DESC)
        foreach ($statusGroups as &$group) {
            usort($group, function ($a, $b) {
                $aLatestMessageTimestamp = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $a['latestMessage']['createdOn']);
                $bLatestMessageTimestamp = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $b['latestMessage']['createdOn']);
                return $bLatestMessageTimestamp->getTimestamp() - $aLatestMessageTimestamp->getTimestamp();
            });
        }

        // Flatten the sorted groups back into a single array, using the $order defined above
        return array_reduce($order, function ($carry, $status) use ($statusGroups) {
            return array_merge($carry, $statusGroups[$status]);
        }, array());
    }

    private function getRepository(): ConversationRepo
    {
        $conversationRepository = $this->getRepo('Conversation');
        assert($conversationRepository instanceof ConversationRepo);
        return $conversationRepository;
    }
}
