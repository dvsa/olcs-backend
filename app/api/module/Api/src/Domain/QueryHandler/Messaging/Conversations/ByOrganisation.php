<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations;

use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByOrganisation as GetConversationsByOrganisationQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByOrganisation extends AbstractConversationQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [ConversationRepo::class, MessageRepo::class];

    /** @param GetConversationsByOrganisationQuery|QueryInterface $query */
    public function handleQuery(QueryInterface $query): array
    {
        $conversationRepository = $this->getRepo(ConversationRepo::class);
        $messageRepository = $this->getRepo(MessageRepo::class);

        $conversationsQuery = $conversationRepository->getByOrganisationId($query, (int)$query->getOrganisation());
        $conversationsQuery = $conversationRepository->filterByStatuses($conversationsQuery, $query->getStatuses());
        $conversations = $conversationRepository->fetchPaginatedList($conversationsQuery);

        foreach ($conversations as $key => $value) {
            $unreadMessageCount = count(
                $messageRepository->getUnreadMessagesByConversationIdAndUserId($value['id'], $this->getUser()->getId())
            );
            $conversations[$key]['userContextUnreadCount'] = $unreadMessageCount;
            $conversations[$key]['userContextStatus'] = $this->stringifyMessageStatusForUser($value, $unreadMessageCount);
            $conversations[$key]['latestMessage'] = $messageRepository->getLastMessageByConversationId((int)$value['id']);
        }

        $conversations = $this->orderResultPrioritisingNewMessages($conversations);

        return [
            'result' => $conversations,
            'count' => $conversationRepository->fetchPaginatedCount($conversationsQuery),
        ];
    }
}
