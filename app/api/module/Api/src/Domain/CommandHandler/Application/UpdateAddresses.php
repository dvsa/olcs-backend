<?php

/**
 * Application Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
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
        $this->result = new Result();
        $this->result->merge(
            $this->getCommandHandler()->handleCommand(
                SaveAddresses::create($command->getArrayCopy())
            )
        );

        // @TODO application completion

        return $this->result;
    }
}
