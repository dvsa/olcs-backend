<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository\DataRetention;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\DataRetention\DataRetention as DataRetentionEntity;
use Dvsa\Olcs\Transfer\Command\DataRetention\DelayItems as DelayItemsCommand;

/**
 * Class DelayItems
 */
final class DelayItems extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'DataRetention';

    /**
     * Handle command
     *
     * @param CommandInterface|DelayItemsCommand $command DTO
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

            $dataRetentionRecord->setNextReviewDate($command->getNextReviewDate());

            // Update entity
            $this->getRepo()->save($dataRetentionRecord);
        }

        return (new Result())
            ->addMessage(count($command->getIds()) . ' Data retention record(s) updated');
    }
}
