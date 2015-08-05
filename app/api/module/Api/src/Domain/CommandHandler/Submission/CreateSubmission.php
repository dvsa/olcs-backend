<?php

/**
 * Create Submission
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command\Submission\CreateSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;

/**
 * Create Submission
 */
final class CreateSubmission extends AbstractCommandHandler
{
    protected $repoServiceName = 'Submission';

    public function handleCommand(CommandInterface $command)
    {
        $submission = $this->createSubmission($command);

        $this->getRepo()->save($submission);

        $result = new Result();
        $result->addId('submission', $submission->getId());
        $result->addMessage('Submission created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Submission
     */
    private function createSubmission(Cmd $command)
    {
        $dataSnapshot = $this->generateDataSnapshot($command);

        $submission = new Submission(
            $this->getRepo()->getReference(CasesEntity::class, $command->getCase()),
            $command->getSubmissionType(),
            $dataSnapshot
        );

        if ($command->getRecipientUser() !== null) {
            $submission->setRecipientUser($this->getRepo()->getReference(UserEntity::class, $command->getRecipientUser()));
        }

        if ($command->getSenderUser() !== null) {
            $submission->setSenderUser($this->getRepo()->getReference(UserEntity::class, $command->getSenderUser()));
        }

        if ($command->getClosedDate() !== null) {
            $submission->setClosedDate($command->getClosedDate());
        }

        if ($command->getUrgent() !== null) {
            $submission->setUrgent($command->getUrgent());
        }

        return $submission;
    }

    private function generateDataSnapshot($command)
    {
        return '';
    }
}
