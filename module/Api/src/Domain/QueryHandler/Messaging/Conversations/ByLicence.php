<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations;

use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByLicence as GetConversationsByLicenceQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByLicence extends AbstractConversationQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

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
            $conversations[$key]['latestMessage'] = $this->getLatestMessageMetadata((int)$value['id']);
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

    private function getLatestMessageMetadata(int $conversationId): array
    {
        $messageRepository = $this->getRepo('Message');
        return $messageRepository->getLastMessageByConversationId($conversationId);
    }

    private function getRepository(): ConversationRepo
    {
        return $this->getRepo('Conversation');
    }
}
