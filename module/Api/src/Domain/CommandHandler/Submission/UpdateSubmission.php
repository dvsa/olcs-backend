<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;

/**
 * Update Submission
 */
final class UpdateSubmission extends AbstractCommandHandler implements SubmissionGeneratorAwareInterface
{
    use SubmissionGeneratorAwareTrait;

    protected $repoServiceName = 'Submission';

    /**
     * Handle
     * @param TransferCmd\Submission\UpdateSubmission $command Command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $submissionEntity = $this->updateSubmission($command);

        $submissionEntity = $this->getSubmissionGenerator()->generateSubmission(
            $submissionEntity,
            $command->getSections()
        );

        $this->getRepo()->save($submissionEntity);

        $result = new Result();
        $result->addId('submission', $submissionEntity->getId());
        $result->addMessage('Submission updated successfully');

        return $result;
    }

    /**
     * Update submission
     *
     * @param TransferCmd\Submission\UpdateSubmission $command Command
     *
     * @return Submission
     */
    private function updateSubmission(TransferCmd\Submission\UpdateSubmission $command)
    {
        /** @var Submission $submission */
        $submission = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($command->getSubmissionType() !== null) {
            $submission->setSubmissionType($this->getRepo()->getRefdataReference($command->getSubmissionType()));
        }

        if ($command->getRecipientUser() !== null) {
            $submission->setRecipientUser(
                $this->getRepo()->getReference(UserEntity::class, $command->getRecipientUser())
            );
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
}
