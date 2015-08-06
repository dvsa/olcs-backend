<?php

/**
 * Update Submission
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command\Submission\UpdateSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Doctrine\ORM\Query;

/**
 * Update Submission
 */
final class UpdateSubmission extends AbstractCommandHandler
{
    protected $repoServiceName = 'Submission';

    public function handleCommand(CommandInterface $command)
    {
        $submission = $this->updateSubmission($command);

        $this->getRepo()->save($submission);

        $result = new Result();
        $result->addId('submission', $submission->getId());
        $result->addMessage('Submission updated successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Submission
     */
    private function updateSubmission(Cmd $command)
    {
        $submission = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $dataSnapshot = $this->generateDataSnapshot($command);

        if ($command->getSubmissionType() !== null) {
            $submission->setSubmissionType($this->getRepo()->getRefdataReference($command->getSubmissionType()));
        }

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
        return 'THIS IS AN UPDATED TEST';
    }
}
