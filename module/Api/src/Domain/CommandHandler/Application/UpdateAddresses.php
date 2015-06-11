<?php

/**
 * Application Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Application Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class UpdateAddresses extends AbstractCommandHandler
{
    public function handleCommand(CommandInterface $command)
    {
        $result = $this->getCommandHandler()->handleCommand(
            SaveAddresses::create($command->getArrayCopy())
        );

        $result->merge(
            $this->getCommandHandler()->handleCommand(
                UpdateApplicationCompletionCommand::create(
                    ['id' => $command->getId(), 'section' => 'addresses']
                )
            )
        );

        return $result;
    }
}
