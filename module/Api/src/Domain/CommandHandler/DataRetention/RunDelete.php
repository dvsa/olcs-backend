<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Queue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class RunDelete
 */
final class RunDelete extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'DataRetentionRule';

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        // @todo Main part of the service bo be done in JIRA OLCS-16626

        $params = [
            'type' => Entity\Queue\Queue::TYPE_REMOVE_DELETED_DOCUMENTS,
            'status' => Entity\Queue\Queue::STATUS_QUEUED,
        ];
        $this->handleSideEffect(Queue\Create::create($params));

        return $this->result;
    }
}
