<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ConditionUndertaking;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete a list of ConditionUndertaking
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class DeleteList extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ConditionUndertaking';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $cuId) {
            /* @var $conditionUndertaking \Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking */
            $conditionUndertaking = $this->getRepo()->fetchById($cuId);
            $this->getRepo()->delete($conditionUndertaking);

            $result->addMessage("ConditionUndertaking ID {$cuId} deleted");
        }

        return $result;
    }
}
