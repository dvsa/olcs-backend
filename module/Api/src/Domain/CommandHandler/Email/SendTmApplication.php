<?php

/**
 * Send Transport Manager Application Email
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

/**
 * Send Transport Manager Application Email
 *
 * @todo This needs implementing
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class SendTmApplication extends AbstractCommandHandler
{
    protected $repoServiceName = 'TransportManagerApplication';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        //$tma = $this->getRepo()->fetchUsingId($command);

        //$testMessage = "Send message to TMA {$command->getId()}";
        //file_put_contents('/tmp/'. uniqid(), $testMessage);

        $result = new Result();
        $result->addMessage('SendTmApplication needs to be implemented.');
        return $result;
    }
}
