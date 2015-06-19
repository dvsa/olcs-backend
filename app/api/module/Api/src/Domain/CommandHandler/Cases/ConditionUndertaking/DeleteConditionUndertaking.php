<?php

/**
 * Delete ConditionUndertaking
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ConditionUndertaking;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Cases\Complaint\DeleteConditionUndertaking as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Delete ConditionUndertaking
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class DeleteConditionUndertaking extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ConditionUndertaking';

    /**
     * Delete ConditionUndertaking
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $conditionUndertaking = $this->getRepo()->fetchUsingId(
            $command,
            Query::HYDRATE_OBJECT,
            $command->getVersion()
        );

        $this->getRepo()->delete($conditionUndertaking);

        $result->addMessage('ConditionUndertaking deleted');

        return $result;
    }
}
