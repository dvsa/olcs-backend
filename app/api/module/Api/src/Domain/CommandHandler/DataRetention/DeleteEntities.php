<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

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
final class DeleteEntities extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'DataRetention';

    protected $extraRepos = ['Queue', 'SystemParameter'];

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $limit = (int)$command->getLimit();
        $systemUserId = $this->getRepo('SystemParameter')->getSystemDataRetentionUser();

        /** @var DataRetention $repo */
        $repo = $this->getRepo();

        $repo->runCleanupProc($limit, $systemUserId);

        // Create queue job to remove deleted documents, if not already exists
        if (!$this->getRepo('Queue')->isItemTypeQueued(Queue::TYPE_REMOVE_DELETED_DOCUMENTS)) {
            $command = Create::create(
                ['type' => Queue::TYPE_REMOVE_DELETED_DOCUMENTS, 'status' => Queue::STATUS_QUEUED]
            );
            $this->handleSideEffect($command);
        }

        return $this->result;
    }
}
