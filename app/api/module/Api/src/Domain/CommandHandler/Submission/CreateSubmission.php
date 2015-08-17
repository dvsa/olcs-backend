<?php

/**
 * Create Submission
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Transfer\Command\Submission\CreateSubmission as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;

/**
 * Create Submission
 */
final class CreateSubmission extends AbstractCommandHandler implements SubmissionGeneratorAwareInterface
{
    use SubmissionGeneratorAwareTrait;

    protected $repoServiceName = 'Submission';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var SubmissionEntity $submissionEntity */
        $submissionEntity = $this->createSubmission($command);

        $submissionEntity->setDataSnapshot(
            $this->getSubmissionGenerator()->generateSubmission($submissionEntity, $command->getSections())
        );
        //$this->getRepo()->save($submission);

        $result = new Result();
        $result->addId('submission', $submissionEntity->getId());
        $result->addMessage('Submission created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Submission
     */
    private function createSubmission(Cmd $command)
    {
        $submissionEntity = new SubmissionEntity(
            $this->getRepo()->getReference(CasesEntity::class, $command->getCase()),
            $this->getRepo()->getRefdataReference($command->getSubmissionType())
        );

        return $submissionEntity;
    }

    private function generateDataSnapshot($command)
    {
        return 'THIS IS A TEST';
    }
}
