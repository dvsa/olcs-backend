<?php

/**
 * Assign Submission
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command\Submission\AssignSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Mockery\CountValidator\Exception;

/**
 * Assign Submission
 */
final class AssignSubmission extends AbstractCommandHandler implements
    AuthAwareInterface,
    SubmissionGeneratorAwareInterface,
    TransactionedInterface
{
    use SubmissionGeneratorAwareTrait;
    use AuthAwareTrait;

    protected $repoServiceName = 'Submission';

    protected $extraRepos = ['User'];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $submissionEntity = $this->updateSubmission($command);

        $this->getRepo()->save($submissionEntity);

        $result = new Result();
        $result->addId('submission', $submissionEntity->getId());
        $result->addMessage('Submission updated successfully');

        $result->merge($this->handleSideEffect($this->createCreateTaskCommand($command)));

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Submission
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function updateSubmission(Cmd $command)
    {
        /** @var Submission $submission */
        $submission = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        if (empty($submission->getInformationCompleteDate())) {
            throw new ValidationException(
                [
                    'NOT_COMPLETE' => 'Cannot assign submission until information complete date is set'
                ]
            );
        }
        $submission->setRecipientUser(
            $this->getRepo()->getReference(UserEntity::class, $command->getRecipientUser())
        );

        $submission->setAssignedDate(new \DateTime('now'));

        $currentUser = $this->getCurrentUser();

        $submission->setSenderUser($currentUser);

        if ($command->getUrgent() !== null) {
            $submission->setUrgent($command->getUrgent());
        }

        return $submission;
    }

    /**
     * @param Cmd $command
     * @return static
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createCreateTaskCommand(Cmd $command)
    {
        $submission = $this->getRepo()->fetchWithCaseAndLicenceById($command->getId());

        /** @var UserEntity $recipientUser */
        $recipientUser = $this->getRepo('User')->fetchById($command->getRecipientUser());

        $description = 'Licence ' . $submission->getCase()->getLicence()->getId() .
            ' Case ' . $submission->getCase()->getId() .
            ' Submission ' . $submission->getId();

        $teamId = null;
        if ($recipientUser->getTeam() instanceof TeamEntity) {
            $teamId = $recipientUser->getTeam()->getId();
        }
        $data = [
            'category' => TaskEntity::CATEGORY_SUBMISSION,
            'subCategory' => TaskEntity::SUBCATEGORY_SUBMISSION_ASSIGNMENT,
            'description' => $description,
            'actionDate' => date('Y-m-d'),
            'assignedToUser' => $recipientUser->getId(),
            'assignedToTeam' => $teamId,
            'assignedByUser' => $this->getCurrentUser()->getId(),
            'case' => $submission->getCase()->getId(),
            'submission' => $submission->getId(),
            'licence' => $submission->getCase()->getLicence()->getId(),
            'urgent' => $command->getUrgent(),
            'isClosed' => 0
        ];

        return CreateTaskCmd::create($data);
    }
}
