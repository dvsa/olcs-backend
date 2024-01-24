<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Conversation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\Operator\UnlicensedAbstract as AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingSubject;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Messaging\Conversation\Create as CreateConversationCommand;
use Dvsa\Olcs\Transfer\Command\Messaging\Message\Create as CreateMessageCommand;

final class Create extends AbstractCommandHandler
{
    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [
        Repository\Conversation::class,
        Repository\Task::class,
        Repository\MessagingSubject::class,
    ];

    /**
     * @param $command CreateConversationCommand
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        $messageSubject = $this->getMessageSubject($command);

        $createTaskResult = $this->handleSideEffect(CreateTask::create([
            'category'    => $messageSubject->getCategory()->getId(),
            'subCategory' => $messageSubject->getSubCategory()->getId(),
            'licence'     => $command->getLicence(),
            'application' => $command->getApplication(),
        ]));

        $conversation = $this->generateAndSaveConversation($command, $createTaskResult, $messageSubject);

        $createMessageResult = $this->handleSideEffect(CreateMessageCommand::create([
            'conversation'   => $conversation->getId(),
            'messageContent' => $command->getMessageContent(),
        ]));

        $result = new Result();

        $result
            ->addId('conversation', $conversation->getId())
            ->addMessage('Conversation added');

        $result->merge($createTaskResult);
        $result->merge($createMessageResult);

        return $result;
    }

    /**
     * @throws NotFoundException
     */
    private function getMessageSubject(CreateConversationCommand $command): MessagingSubject
    {
        return $this->getMessagingSubjectRepo()->fetchById($command->getMessageSubject());
    }

    private function generateAndSaveConversation(CreateConversationCommand $command, Result $createTaskResult, MessagingSubject $messageSubject): MessagingConversation
    {
        $task = $this->getTask($createTaskResult->getId('task'));
        $subject = $this->generateConversationSubjectFromMessageSubject($messageSubject);
        $conversation = $this->createConversationEntity($task, $subject);

        $this->getConversationRepo()->save($conversation);

        return $conversation;
    }

    private function getTask(int $taskId): Task
    {
        $taskRepo = $this->getTaskRepo();
        return $taskRepo->fetchById($taskId);
    }

    private function getTaskRepo(): Repository\Task
    {
        return $this->getRepo(Repository\Task::class);
    }

    private function generateConversationSubjectFromMessageSubject(MessagingSubject $messageSubject): string
    {
        return sprintf(
            '%s enquiry',
            $messageSubject->getDescription()
        );
    }

    private function createConversationEntity(Task $task, string $subject): MessagingConversation
    {
        $entity = new MessagingConversation();
        $entity
            ->setTask($task)
            ->setSubject($subject);
        return $entity;
    }

    private function getConversationRepo(): Repository\Conversation
    {
        return $this->getRepo(Repository\Conversation::class);
    }

    private function getMessagingSubjectRepo(): Repository\MessagingSubject
    {
        return $this->getRepo(Repository\MessagingSubject::class);
    }
}
