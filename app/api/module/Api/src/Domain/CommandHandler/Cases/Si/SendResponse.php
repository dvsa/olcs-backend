<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * SendResponse
 *
 * @todo This is to be implemented when the NR is migrated to backend
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class SendResponse extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Cases';

    /**
     * SendResponse
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->result->addMessage('SendResponse To be implemtend');

        return $this->result;
    }
}
