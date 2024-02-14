<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Messaging;

use Dvsa\Olcs\Api\Domain\Repository\Message;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGenerator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGeneratorServices;
use Dvsa\Olcs\Transfer\Query\Messaging\Messages\ByConversation;

class Generator extends AbstractGenerator
{
    protected Message $messageRepository;

    public function __construct(
        AbstractGeneratorServices $abstractGeneratorServices,
        Message $messageRepository
    ) {
        parent::__construct($abstractGeneratorServices);

        $this->messageRepository = $messageRepository;
    }

    public function generate(MessagingConversation $conversation): string
    {
        $query = $this->messageRepository->getBaseMessageListWithContentQuery(
            ByConversation::create(
                [
                    'page'  => 1,
                    'limit' => 1000,
                ],
            ),
        );
        $query = $this->messageRepository->filterByConversationId($query, $conversation->getId());
        /** @var MessagingMessage[] $messages */
        $messages = $this->messageRepository->fetchPaginatedList($query);

        return $this->generateReadonly(
            [
                'conversation' => $conversation,
                'messages'     => $messages,
            ],
            'conversation',
        );
    }
}
