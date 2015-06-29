<?php

/**
 * Update SubmissionAction
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Pi\Reason;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update SubmissionAction
 */
final class UpdateSubmissionAction extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'SubmissionAction';

    public function handleCommand(CommandInterface $command)
    {
        $submissionAction = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $actionTypes = array_map(
            function ($actionTypeId) {
                return $this->getRepo()->getRefdataReference($actionTypeId);
            },
            $command->getActionTypes()
        );

        $submissionAction->update($actionTypes, $command->getComment());

        if ($command->getReasons() !== null) {
            $reasons = array_map(
                function ($reasonId) {
                    return $this->getRepo()->getReference(Reason::class, $reasonId);
                },
                $command->getReasons()
            );
            $submissionAction->setReasons($reasons);
        }

        $this->getRepo()->save($submissionAction);

        $result = new Result();
        $result->addId('submissionAction', $submissionAction->getId());
        $result->addMessage('Submission Action updated successfully');

        return $result;
    }
}
