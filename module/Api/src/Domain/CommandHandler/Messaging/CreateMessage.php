<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging;

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
use Dvsa\Olcs\Transfer\Command\Messaging\CreateMessage as CreateMessageCommand;

final class CreateMessage extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [
        ConversationRepo::class,
        MessageRepo::class,
        MessageContentRepo::class,
    ];

    public function handleCommand(CommandInterface $command)
    {
        assert($command instanceof CreateMessageCommand);

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
            throw new BadRequestException('Unable to create message on conversations that are closed or archived');
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
        $repo = $this->getRepo(MessageContentRepo::class);
        assert($repo instanceof MessageContentRepo);
        return $repo;
    }

    private function getMessageRepository(): MessageRepo
    {
        $repo = $this->getRepo(MessageRepo::class);
        assert($repo instanceof MessageRepo);
        return $repo;
    }
}
