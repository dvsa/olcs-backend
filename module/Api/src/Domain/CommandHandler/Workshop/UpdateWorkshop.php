<?php

/**
 * Update Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Workshop;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Licence\Workshop;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Update Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateWorkshop extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Workshop';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Workshop $workshop */
        $workshop = $this->getRepo()->fetchUsingId($command);

        // Update the address
        $addressData = $command->getContactDetails()['address'];
        $addressResult = $this->handleSideEffect(SaveAddress::create($addressData));
        $result->merge($addressResult);

        // Update the Contact Details
        $contactDetails = $workshop->getContactDetails();
        $contactDetails->setFao($command->getContactDetails()['fao']);

        // Update the workshop
        $workshop->setIsExternal($command->getIsExternal());
        $this->getRepo()->save($workshop);

        $result->addMessage('Workshop updated');

        return $result;
    }
}
