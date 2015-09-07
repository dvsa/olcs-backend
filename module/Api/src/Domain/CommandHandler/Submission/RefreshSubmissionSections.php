<?php

/**
 * Refresh Submission Sections
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command\Submission\RefreshSubmissionSections as Cmd;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;

/**
 * Refresh Submission
 */
final class RefreshSubmissionSections extends AbstractCommandHandler implements SubmissionGeneratorAwareInterface
{
    use SubmissionGeneratorAwareTrait;

    protected $repoServiceName = 'Submission';

    public function handleCommand(CommandInterface $command)
    {
        $submissionEntity = $this->refreshSubmission($command);

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
    private function refreshSubmission(Cmd $command)
    {
        /** @var Submission $submissionEntity */
        $submissionEntity = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // set section data prior to update in order to retain any other sections
        $dataSnapshot = json_decode($submissionEntity->getDataSnapshot(), true);
        foreach ($dataSnapshot as $section => $sectionData) {
            $submissionEntity->setSectionData($section, $sectionData);
        }

        $submissionEntity = $this->getSubmissionGenerator()->generateSubmission(
            $submissionEntity,
            $command->getSections()
        );

        return $submissionEntity;
    }
}
