<?php

/**
 * Transport Manager / Create
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Transfer\Command\Tm\Create as Cmd;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as SaveAddressCmd;
use Dvsa\Olcs\Api\Domain\Command\Person\Create as CreatePersonCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Transport Manager / Create
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TransportManager';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $saveWorkAddress = SaveAddressCmd::create(
            [
                'addressLine1' => $command->getWorkAddressLine1(),
                'addressLine2' => $command->getWorkAddressLine2(),
                'addressLine3' => $command->getWorkAddressLine3(),
                'addressLine4' => $command->getWorkAddressLine4(),
                'town'         => $command->getWorkTown(),
                'postcode'     => $command->getWorkPostcode(),
                'countryCode'  => $command->getWorkCountryCode(),
                'contactType'  => ContactDetailsEntity::CONTACT_TYPE_TRANSPORT_MANAGER
            ]
        );
        $workAddressResult = $this->getCommandHandler()->handleCommand($saveWorkAddress);

        $saveHomeAddress = SaveAddressCmd::create(
            [
                'addressLine1' => $command->getHomeAddressLine1(),
                'addressLine2' => $command->getHomeAddressLine2(),
                'addressLine3' => $command->getHomeAddressLine3(),
                'addressLine4' => $command->getHomeAddressLine4(),
                'town'         => $command->getHomeTown(),
                'postcode'     => $command->getHomePostcode(),
                'countryCode'  => $command->getHomeCountryCode(),
                'contactType'  => ContactDetailsEntity::CONTACT_TYPE_TRANSPORT_MANAGER
            ]
        );
        $homeAddressResult = $this->getCommandHandler()->handleCommand($saveHomeAddress);

        $createPerson = CreatePersonCmd::create(
            [
                'firstName'  => $command->getFirstName(),
                'lastName'   => $command->getLastName(),
                'title'      => $command->getTitle(),
                'birthDate'  => $command->getBirthDate(),
                'birthPlace' => $command->getBirthPlace()
            ]
        );
        $personResult = $this->getCommandHandler()->handleCommand($createPerson);

        $this->updateHomeContactDetails(
            $homeAddressResult->getId('contactDetails'),
            $personResult->getId('person'),
            $command
        );

        $transportManager = $this->createTransportManagerObject(
            $command,
            $workAddressResult->getId('contactDetails'),
            $homeAddressResult->getId('contactDetails')
        );

        $this->getRepo()->save($transportManager);

        $result->addId('transportManager', $transportManager->getId());
        $result->addMessage('Transport Manager created successfully');
        $result->merge($personResult);

        // need to add ids and messages manually, otherewise it will be overwritten
        $result->addId('workAddress', $workAddressResult->getId('address'));
        $result->addId('workContactDetails', $workAddressResult->getId('contactDetails'));
        $result->addId('homeAddress', $homeAddressResult->getId('address'));
        $result->addId('homeContactDetails', $homeAddressResult->getId('contactDetails'));
        $result->addMessage('Work address added');
        $result->addMessage('Home address added');
        $result->addMessage('Work contact details added');
        $result->addMessage('Home contact details added');

        return $result;
    }

    protected function updateHomeContactDetails($contactDetailsId, $personId, $command)
    {
        $contactDetails = $this->getRepo('ContactDetails')->fetchById($contactDetailsId);
        $contactDetails->updateContactDetailsWithPersonAndEmailAddress(
            $this->getRepo()->getReference(PersonEntity::class, $personId),
            $command->getEmailAddress()
        );
        $this->getRepo('ContactDetails')->save($contactDetails);
    }

    /**
     * @param Cmd $command
     * @return TransportManagerEntity
     */
    private function createTransportManagerObject($command, $workCdId, $homeCdId)
    {
        $transportManager = new TransportManagerEntity();
        $status = $command->getStatus() ? $command->getStatus() :
            TransportManagerEntity::TRANSPORT_MANAGER_STATUS_ACTIVE;
        $transportManager->updateTransportManager(
            $this->getRepo()->getRefdataReference($command->getType()),
            $this->getRepo()->getRefdataReference($status),
            $this->getRepo()->getReference(ContactDetailsEntity::class, $workCdId),
            $this->getRepo()->getReference(ContactDetailsEntity::class, $homeCdId),
            $this->getCurrentUser()
        );
        return $transportManager;
    }
}
