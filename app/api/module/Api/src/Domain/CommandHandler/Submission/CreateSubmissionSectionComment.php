<?php

/**
 * Create SubmissionSectionComment
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\SubmissionSectionComment;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command\Submission\CreateSubmissionSectionComment as Cmd;

/**
 * Create SubmissionSectionComment
 */
final class CreateSubmissionSectionComment extends AbstractCommandHandler
{
    protected $repoServiceName = 'SubmissionSectionComment';

    public function handleCommand(CommandInterface $command)
    {
        $submissionSectionComment = $this->createSubmissionSectionComment($command);

        $this->getRepo()->save($submissionSectionComment);

        $result = new Result();
        $result->addId('submissionSectionComment', $submissionSectionComment->getId());
        $result->addId('submissionSection', $submissionSectionComment->getSubmissionSection()->getId());
        $result->addMessage('Submission section comment created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return SubmissionSectionComment
     */
    private function createSubmissionSectionComment(Cmd $command)
    {
        $submissionSectionComment = new SubmissionSectionComment(
            $this->getRepo()->getReference(Submission::class, $command->getSubmission()),
            $this->getRepo()->getRefdataReference($command->getSubmissionSection())
        );

        if ($command->getComment() !== null) {
            $submissionSectionComment->setComment($command->getComment());
        }

        return $submissionSectionComment;
    }
}
