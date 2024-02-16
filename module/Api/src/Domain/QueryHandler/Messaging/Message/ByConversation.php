<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation;
use Dvsa\Olcs\Api\Domain\Repository\Message;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByConversation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected array $toggleConfig = [FeatureToggle::MESSAGING];

    protected $extraRepos = [Message::class, Conversation::class];

    public function handleQuery(QueryInterface $query)
    {
        $messageRepository = $this->getRepo(Message::class);

        $messageQueryBuilder = $messageRepository->getBaseMessageListWithContentQuery($query);

        $messagesQuery = $messageRepository->filterByConversationId($messageQueryBuilder, $query->getConversation());

        $messages = $messageRepository->fetchPaginatedList($messagesQuery);

        /** @var MessagingConversation $conversation */
        $conversation = $this->getRepo(Conversation::class)->fetchById($query->getConversation());

        return [
            'result'       => $messages,
            'count'        => $messageRepository->fetchPaginatedCount($messagesQuery),
            'conversation' => $conversation->serialize(),
        ];
    }
}
