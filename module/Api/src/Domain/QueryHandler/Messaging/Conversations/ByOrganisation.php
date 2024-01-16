<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations;

use ArrayIterator;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByOrganisation as GetConversationsByOrganisationQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByOrganisation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    private const STATUS_CLOSED = "CLOSED";
    private const STATUS_NEW_MESSAGE = "NEW_MESSAGE";
    private const STATUS_OPEN = "OPEN";

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = ['Conversation', 'Message'];

    /** @param GetConversationsByOrganisationQuery|QueryInterface $query */
    public function handleQuery(QueryInterface $query): array
    {
        $conversationRepository = $this->getRepo('Conversation');
        $messageRepository = $this->getRepo('Message');

        $conversationsQuery = $conversationRepository->getByOrganisationId($query, (int)$query->getOrganisation());
        $conversations = $conversationRepository->fetchPaginatedList($conversationsQuery);

        foreach ($conversations as $key => $value) {
            $unreadMessageCount = count(
                $messageRepository->getUnreadMessagesByConversationIdAndUserId($value['id'], $this->getUser()->getId())
            );
            $conversations[$key]['userContextUnreadCount'] = $unreadMessageCount;
            $conversations[$key]['userContextStatus'] = $this->stringifyMessageStatusForUser($value, $unreadMessageCount);
            $conversations[$key]['latestMessage'] = $messageRepository->getLastMessageByConversationId($value['id']);
        }

        $conversations = $this->orderResultPrioritisingNewMessages($conversations);

        return [
            'result' => $conversations,
            'count' => $conversationRepository->fetchPaginatedCount($conversationsQuery),
        ];
    }

    private function stringifyMessageStatusForUser(array $conversation, int $count): string
    {
        if ($conversation['isClosed']) {
            return self::STATUS_CLOSED;
        }
        if ($count > 0) {
            return self::STATUS_NEW_MESSAGE;
        }
        return self::STATUS_OPEN;
    }

    /**
     * This method takes a conversation list, and returns sorted based on the following rules:
     *
     *  - Sort by conversation status in order (NEW_MESSAGE, OPEN, CLOSED)
     *  - Within the status groups, sort by latest message creation timestamp descending (newest first).
     */
    private function orderResultPrioritisingNewMessages(ArrayIterator $conversationList): array
    {
        if (count($conversationList) < 2) {
            return iterator_to_array($conversationList);
        }

        $order = [self::STATUS_NEW_MESSAGE, self::STATUS_OPEN, self::STATUS_CLOSED];

        // Separate the data into groups based on 'userContextStatus'
        $statusGroups = array_fill_keys($order, []);
        foreach ($conversationList as $item) {
            $status = $item['userContextStatus'];
            $statusGroups[$status][] = $item;
        }

        // Sort each group by latest message created on timestamp (DESC)
        foreach ($statusGroups as &$group) {
            usort($group, fn($a, $b) => $b['latestMessage']['createdOn'] <=> $a['latestMessage']['createdOn']);
        }

        // Flatten the sorted groups back into a single array, using the $order defined above
        return array_reduce($order, fn($carry, $status) => array_merge($carry, $statusGroups[$status]), []);
    }
}
