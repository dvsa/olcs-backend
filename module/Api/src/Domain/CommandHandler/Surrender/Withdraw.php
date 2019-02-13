<?php


namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;


use Dvsa\Olcs\Transfer\Command\CommandInterface;

class Withdraw extends AbstractSurrenderCommandHandler
{

    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {

    }
}