<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging;

use Doctrine\ORM\EntityManagerInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\Operator\UnlicensedAbstract as AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation;
use Dvsa\Olcs\Api\Domain\Repository\Task as TaskRepository;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingContent;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Messaging\CreateMessage as CreateMessageCommand;
use Interop\Container\ContainerInterface;

final class CreateConversation extends AbstractCommandHandler
{
    protected $extraRepos = ['Conversation', 'Task'];
    private EntityManagerInterface $entityManager;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->entityManager = $container->get('doctrine.entitymanager.orm_default');

        return parent::__invoke($container, $requestedName, $options);
    }

    public function handleCommand(CommandInterface $command)
    {
        assert($command instanceof CreateMessageCommand);

        // TODO: Create a task using category, subcategory, licence, application from command.
        $createTaskResult = $this->handleSideEffect(CreateTask::create([

        ]));

        $conversation = $this->generateAndSaveConversation($command, $createTaskResult);

        // TODO: Use messageContent from CreateConversationCommand
        $createMessageResult = $this->handleSideEffect(CreateMessageCommand::create([
            'conversation' => $conversation->getId(),
            'messageContent' => 'This is an example message generated from a CreateConversation command via side effect'
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
        $taskRepo = $this->getRepo('Task');
        assert($taskRepo instanceof TaskRepository);

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

    private function generateAndSaveConversation(CreateMessageCommand $command, Result $createTaskResult): MessagingConversation
    {
        $task = $this->getTask($createTaskResult->getId('task'));
        $subject = $this->generateConversationSubjectFromTask($task);
        $conversation = $this->createConversationEntity($task, $subject);

        $this->entityManager->persist($conversation);
        $this->entityManager->flush();

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
}
