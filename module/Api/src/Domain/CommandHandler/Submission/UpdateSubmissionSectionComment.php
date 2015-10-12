<?php

/**
 * Update SubmissionSectionComment
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\SubmissionSectionComment;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\Submission\UpdateSubmissionSectionComment as Cmd;

/**
 * Update SubmissionSectionComment
 */
final class UpdateSubmissionSectionComment extends AbstractCommandHandler
{
    protected $repoServiceName = 'SubmissionSectionComment';

    public function handleCommand(CommandInterface $command)
    {
        $submissionSectionComment = $this->createSubmissionSectionComment($command);

        $this->getRepo()->save($submissionSectionComment);

        $result = new Result();
        $result->addId('submissionSectionComment', $submissionSectionComment->getId());
        $result->addId('submissionSection', $submissionSectionComment->getSubmissionSection()->getId());
        $result->addMessage('Submission section comment updated successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return SubmissionSectionComment
     */
    private function createSubmissionSectionComment(Cmd $command)
    {
        $submissionSectionComment = $this->getRepo()->fetchUsingId(
            $command,
            Query::HYDRATE_OBJECT, $command->getVersion()
        );

        if ($command->getComment() !== null) {
            $submissionSectionComment->setComment($command->getComment());
        }

        return $submissionSectionComment;
    }
}
