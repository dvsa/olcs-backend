<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository\DataRetention;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\DataRetention\DataRetention as DataRetentionEntity;
use Dvsa\Olcs\Transfer\Command\DataRetention\UpdateActionConfirmation as Command;

/**
 * Class UpdateActonConfirmation
 */
final class UpdateActionConfirmation extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'DataRetention';

    /**
     * Handle command
     *
     * @param CommandInterface|Command $command DTO
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var DataRetention $repo */
        $repo = $this->getRepo();

        foreach ($command->getIds() as $id) {
            /** @var DataRetentionEntity $dataRetentionRecord */
            $dataRetentionRecord = $repo->fetchById($id);

            $currentStatus = $dataRetentionRecord->getActionConfirmation();

            // If we cannot delete then do nothing and move to next one
            if (! $this->canDelete($dataRetentionRecord)) {
                continue;
            }

            // If actionConfirmation is true, update status to false.  Otherwise true.
            $status = ($currentStatus)? false : true;

            $dataRetentionRecord->setActionConfirmation($status);
            $dataRetentionRecord->setActionedDate(new \DateTime('now'));

            // Update entity
            $this->getRepo()->save($dataRetentionRecord);
        }

        return (new Result())
            ->addMessage(count($command->getIds()) . ' Data retention record(s) updated');
    }

    /**
     * Validate if a record can be marked as deleted or not
     *
     * @param DataRetentionEntity $record Data retention record entity
     *
     * @return bool
     */
    private function canDelete(DataRetentionEntity $record)
    {
        if ($record->getNextReviewDate() || $record->getActionedDate()) {
            $this->setFalseActionConfirmation($record);
            return false;
        }

        return true;
    }

    /**
     * On some instances/scenarios we cannot mark as delete.  Here we ensure
     * the record 'action_confirmation' value is always set to false.
     *
     * @param DataRetentionEntity $record Data retention record entity
     *
     * @return bool
     */
    private function setFalseActionConfirmation(DataRetentionEntity $record)
    {
        if (! $record->getActionConfirmation()) {
            return true;
        }

        $record->setActionConfirmation(false);
        $record->setActionedDate(new \DateTime('now'));

        return $this->getRepo()->save($record);
    }
}
