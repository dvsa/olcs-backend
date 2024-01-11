<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\Messages\ByConversation as GetConversationMessagesQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByConversation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected array $toggleConfig = [FeatureToggle::MESSAGING];

    protected $extraRepos = ['Conversation', 'Message', 'MessageContent'];

    public function handleQuery(QueryInterface $query)
    {
        assert($query instanceof GetConversationMessagesQuery);
        $messageRepository = $this->getRepo('Message');
        assert($messageRepository instanceof MessageRepo);

        $messageQueryBuilder = $messageRepository->getBaseMessageListWithContentQuery($query);

        $messagesQuery = $messageRepository->filterByConversationId($messageQueryBuilder, $query->getConversation());

        $messages = $messageRepository->fetchPaginatedList($messagesQuery);

        return [
            'result' => $messages,
            'count' => $messageRepository->fetchPaginatedCount($messagesQuery),
            'conversation' => $this->getRepo('Conversation')->fetchById($query->getConversation()),
        ];
    }
}