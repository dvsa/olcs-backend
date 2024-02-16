<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message;

use Doctrine\ORM\NoResultException;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingUserMessageRead;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByConversation extends AbstractQueryHandler implements ToggleRequiredInterface, AuthAwareInterface
{
    use ToggleAwareTrait;
    use AuthAwareTrait;

    protected array $toggleConfig = [FeatureToggle::MESSAGING];

    protected $extraRepos = [
        Repository\Message::class,
        Repository\Conversation::class,
        Repository\MessagingUserMessageRead::class,
    ];

    public function handleQuery(QueryInterface $query)
    {
        $messageRepository = $this->getRepo(Repository\Message::class);

        $messageQueryBuilder = $messageRepository->getBaseMessageListWithContentQuery($query);

        $messagesQuery = $messageRepository->filterByConversationId($messageQueryBuilder, $query->getConversation());

        $messages = $messageRepository->fetchPaginatedList($messagesQuery);

        /** @var MessagingConversation $conversation */
        $conversation = $this->getRepo(Repository\Conversation::class)->fetchById($query->getConversation());

        $this->markMessagesAsReadByCurrentUser($messages);

        return [
            'result'       => $messages,
            'count'        => $messageRepository->fetchPaginatedCount($messagesQuery),
            'conversation' => $conversation->serialize(),
        ];
    }

    private function markMessagesAsReadByCurrentUser(\ArrayIterator $messages): void
    {
        $currentDatetime = new \DateTime();

        foreach ($messages as $message) {
            $messageId = $message['id'];
            $message = $this->getRepo(Repository\Message::class)->fetchById($messageId);
            $messageUserRead = null;
            try {
                $messageUserRead = $this->getRepo(Repository\MessagingUserMessageRead::class)->fetchByMessageIdAndUserId($messageId, $this->getUser()->getId());
            } catch (NoResultException $e) {
                $messageUserRead = new MessagingUserMessageRead();
                $messageUserRead->setMessagingMessage($message);
                $messageUserRead->setUser($this->getUser());
            } finally {
                $messageUserRead->setLastReadOn($currentDatetime);
                $this->getRepo(Repository\MessagingUserMessageRead::class)->save($messageUserRead);
            }
        }
    }
}
