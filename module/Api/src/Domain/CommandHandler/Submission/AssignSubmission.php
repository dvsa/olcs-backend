<?php

/**
 * Assign Submission
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command\Submission\AssignSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Assign Submission
 */
final class AssignSubmission extends AbstractCommandHandler implements AuthAwareInterface,
    SubmissionGeneratorAwareInterface
{
    use SubmissionGeneratorAwareTrait;
    use AuthAwareTrait;

    protected $repoServiceName = 'Submission';

    public function handleCommand(CommandInterface $command)
    {
        $submissionEntity = $this->updateSubmission($command);

        $this->getRepo()->save($submissionEntity);

        $result = new Result();
        $result->addId('submission', $submissionEntity->getId());
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

        if ($command->getRecipientUser() !== null) {
            $submission->setRecipientUser(
                $this->getRepo()->getReference(UserEntity::class, $command->getRecipientUser())
            );
        }

        $currentUser = $this->getCurrentUser();

        $submission->setSenderUser($currentUser);


        if ($command->getUrgent() !== null) {
            $submission->setUrgent($command->getUrgent());
        }

        return $submission;
    }
}
