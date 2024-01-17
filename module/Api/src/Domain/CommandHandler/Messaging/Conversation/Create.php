<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Conversation;

use Doctrine\ORM\EntityManagerInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\Operator\UnlicensedAbstract as AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation;
use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\Repository\MessageContent as MessageContentRepo;
use Dvsa\Olcs\Api\Domain\Repository\Task as TaskRepo;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingContent;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Messaging\Message\Create as CreateMessageCommand;
use Dvsa\Olcs\Transfer\Command\Messaging\Conversation\Create as CreateConversationCommand;
use Interop\Container\ContainerInterface;

final class Create extends AbstractCommandHandler
{
    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [
        ConversationRepo::class,
        TaskRepo::class,
    ];

    public function handleCommand(CommandInterface $command)
    {
        assert($command instanceof CreateConversationCommand);

        $createTaskResult = $this->handleSideEffect(CreateTask::create([
            'category' => $command->getCategory(),
            'subCategory' => $command->getSubCategory(),
            'licence' => $command->getLicence(),
            'application' => $command->getApplication(),
        ]));

        $conversation = $this->generateAndSaveConversation($command, $createTaskResult);

        // TODO: Use messageContent from CreateConversationCommand
        $createMessageResult = $this->handleSideEffect(CreateMessageCommand::create([
            'conversation' => $conversation->getId(),
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

    private function getTask(int $taskId): Task
    {
        $taskRepo = $this->getTaskRepo();

        return $taskRepo->fetchById($taskId);
    }

    private function createConversationEntity(Task $task, string $subject): MessagingConversation
    {
        $entity = new MessagingConversation();
        $entity
            ->setTask($task)
            ->setSubject($subject);
        return $entity;
    }

    private function generateAndSaveConversation(CreateConversationCommand $command, Result $createTaskResult): MessagingConversation
    {
        $task = $this->getTask($createTaskResult->getId('task'));
        $subject = $this->generateConversationSubjectFromTask($task);
        $conversation = $this->createConversationEntity($task, $subject);

        $this->getConversationRepo()->save($conversation);

        return $conversation;
    }

    private function generateConversationSubjectFromTask(Task $task): string
    {
        if (empty($task->getSubCategory())) {
            return sprintf(
                '%s enquiry',
                $task->getCategory()->getDescription()
            );
        }
        return sprintf(
            '%s %s enquiry',
            $task->getCategory()->getDescription(),
            $task->getSubCategory()
        );
    }

    private function getConversationRepo(): ConversationRepo
    {
        $repo = $this->getRepo(ConversationRepo::class);
        assert($repo instanceof ConversationRepo);
        return $repo;
    }

    private function getTaskRepo(): TaskRepo
    {
        $repo = $this->getRepo(TaskRepo::class);
        assert($repo instanceof TaskRepo);
        return $repo;
    }
}
