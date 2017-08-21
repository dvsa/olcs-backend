<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository\DataRetention;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class DeleteEntities
 */
final class DeleteEntities extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    const NUMBER_TO_PROCESS = 10;

    protected $repoServiceName = 'DataRetention';

    protected $extraRepos = ['DataRetentionRule', 'Queue'];

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var DataRetention $repo */
        $repo = $this->getRepo();

        // @TODO update this to reflect new database changes: OLCS-17668
        //        $entitiesToDelete = $repo->fetchEntitiesToDelete(self::NUMBER_TO_PROCESS);
        //
        //        /** @var \Dvsa\Olcs\Api\Entity\DataRetention $dataRetention */
        //        foreach ($entitiesToDelete as $dataRetention) {
        //            $success = $this->getRepo('DataRetentionRule')->runActionProc(
        //                $dataRetention->getDataRetentionRule()->getActionProcedure(),
        //                $dataRetention->getEntityPk(),
        //                $this->getCurrentUser()->getId()
        //            );
        //            $this->result->addMessage(
        //                sprintf(
        //                    '%s data_retention.id = %d, %s',
        //                    $success ? 'SUCCESS' : 'ERROR',
        //                    $dataRetention->getId(),
        //                    $dataRetention->getDataRetentionRule()->getActionProcedure()
        //                )
        //            );
        //        }

        // Create queue job to remove deleted documents, if not already exists
        if (!$this->getRepo('Queue')->isItemTypeQueued(Queue::TYPE_REMOVE_DELETED_DOCUMENTS)) {
            $command = Create::create(
                ['type' => Queue::TYPE_REMOVE_DELETED_DOCUMENTS, 'status' => Queue::STATUS_QUEUED]
            );
            $this->handleSideEffect($command);
        }

        // Are there more entities to delete, if so create another queue job to process
        $moreEntitiesToDelete = $repo->fetchEntitiesToDelete(1);
        if (count($moreEntitiesToDelete) > 0) {
            $command = Create::create(
                ['type' => Queue::TYPE_PROCESS_DATA_RETENTION, 'status' => Queue::STATUS_QUEUED]
            );
            $this->handleSideEffect($command);
        }

        return $this->result;
    }
}
