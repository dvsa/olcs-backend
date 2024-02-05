<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByConversation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected array $toggleConfig = [FeatureToggle::MESSAGING];

    protected $extraRepos = ['Conversation', 'Message', 'MessageContent'];

    public function handleQuery(QueryInterface $query)
    {
        $messageRepository = $this->getRepo('Message');

        $messageQueryBuilder = $messageRepository->getBaseMessageListWithContentQuery($query);

        $messagesQuery = $messageRepository->filterByConversationId($messageQueryBuilder, $query->getConversation());

        $messages = $messageRepository->fetchPaginatedList($messagesQuery);

        /*
         * For _some_ conversations, when sending an authenticated request (so any request from the front end,
         * JSON serializing lastModifiedBy causes a recursion error. An unauthenticated request results in
         * lastModifiedBy always being null.
         */
        /** @var MessagingConversation $conversation */
        $conversation = $this->getRepo('Conversation')->fetchById($query->getConversation());
        $conversation->setLastModifiedBy(null);

        return [
            'result' => $messages,
            'count' => $messageRepository->fetchPaginatedCount($messagesQuery),
            'conversation' => $conversation,
        ];
    }
}
