<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Message;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\Repository\MessageContent as MessageContentRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingContent;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Messaging\Message\Create as CreateMessageCommand;

final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    public const EXCEPTION_MESSAGE_UNABLE_TO_ADD_MESSAGE_TO_CLOSED_ARCHIVED_CONVERSATION = 'Unable to create message on conversations that are closed or archived';

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [
        ConversationRepo::class,
        MessageRepo::class,
        MessageContentRepo::class,
    ];

    public function handleCommand(CommandInterface $command)
    {
        $message = $this->generateAndSaveMessage($command);

        $result = new Result();

        $result
            ->addId('message', $message->getId())
            ->addMessage('Message added')
            ->addId('messageContent', $message->getMessagingContent()->getId())
            ->addMessage('Message Content added')
            ->addId('messageConversation', $message->getMessagingConversation()->getId())
            ->addMessage('Message added to conversation');

        return $result;
    }

    private function generateAndSaveMessage(CreateMessageCommand $command): MessagingMessage
    {
        $conversation = $this->getConversation($command);

        if ($conversation->getIsClosed() || $conversation->getIsArchived()) {
            throw new BadRequestException(self::EXCEPTION_MESSAGE_UNABLE_TO_ADD_MESSAGE_TO_CLOSED_ARCHIVED_CONVERSATION);
        }

        $messageContent = $this->createMessageContentEntity($command->getMessageContent());
        $message = $this->createMessageEntity($conversation, $messageContent);

        $this->getMessageContentRepository()->save($messageContent);
        $this->getMessageRepository()->save($message);

        return $message;
    }

    private function getConversation(CreateMessageCommand $command): MessagingConversation
    {
        return $this->getConversationRepository()->fetchById($command->getConversation());
    }

    private function getConversationRepository(): ConversationRepo
    {
        $repo = $this->getRepo(ConversationRepo::class);
        assert($repo instanceof ConversationRepo);
        return $repo;
    }

    private function createMessageContentEntity(string $text): MessagingContent
    {
        $entity = new MessagingContent();
        $entity
            ->setText($text);
        return $entity;
    }

    private function createMessageEntity(MessagingConversation $messagingConversation, MessagingContent $messagingContent): MessagingMessage
    {
        $entity = new MessagingMessage();
        $entity
            ->setMessagingConversation($messagingConversation)
            ->setMessagingContent($messagingContent);
        return $entity;
    }

    private function getMessageContentRepository(): MessageContentRepo
    {
        return $this->getRepo(MessageContentRepo::class);
    }

    private function getMessageRepository(): MessageRepo
    {
        return $this->getRepo(MessageRepo::class);
    }
}
