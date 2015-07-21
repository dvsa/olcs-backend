<?php

/**
 * Create PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Create as Command;
use Dvsa\Olcs\Api\Entity\ContactDetails;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Create PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'PrivateHireLicence';
    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */
        $result = new Result();

        $address = new \Dvsa\Olcs\Api\Entity\ContactDetails\Address();
        $address->updateAddress(
            $command->getAddress()['addressLine1'],
            $command->getAddress()['addressLine2'],
            $command->getAddress()['addressLine3'],
            $command->getAddress()['addressLine4'],
            $command->getAddress()['town'],
            $command->getAddress()['postcode'],
            $this->getRepo()->getReference(ContactDetails\Country::class, $command->getAddress()['countryCode'])
        );

        $cd = new ContactDetails\ContactDetails(
            $this->getRepo()->getRefdataReference(ContactDetails\ContactDetails::CONTACT_TYPE_HACKNEY)
        );
        $cd->setDescription($command->getCouncilName())
            ->setAddress($address);

        $phl = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl->setLicence($this->getRepo()->getReference(Licence::class, $command->getLicence()))
            ->setPrivateHireLicenceNo($command->getPrivateHireLicenceNo())
            ->setContactDetails($cd);

        $this->getRepo('ContactDetails')->save($cd);
        $this->getRepo()->save($phl);

        $result->addId('address', $address->getId());
        $result->addId('contactDetails', $cd->getId());
        $result->addId('privateHireLicence', $phl->getId());
        $result->addMessage('PrivateHireLicence created');

        return $result;
    }
}
