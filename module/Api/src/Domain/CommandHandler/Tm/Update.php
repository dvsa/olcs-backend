<?php

/**
 * Transport Manager / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as SaveAddressCmd;
use Dvsa\Olcs\Api\Domain\Command\Person\UpdateFull as UpdatePersonCmd;
use Dvsa\Olcs\Transfer\Command\Tm\Update as UpdateTmCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;

/**
 * Transport Manager / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface, CacheAwareInterface
{
    protected $repoServiceName = 'TransportManager';

    protected $extraRepos = ['ContactDetails'];

    use QueueAwareTrait;
    use CacheAwareTrait;

    /**
     * Handle command
     *
     * @param CommandInterface|UpdateTmCmd $command command to update a TM
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $updatePerson = UpdatePersonCmd::create(
            [
                'id'         => $command->getPersonId(),
                'version'    => $command->getPersonVersion(),
                'firstName'  => $command->getFirstName(),
                'lastName'   => $command->getLastName(),
                'title'      => $command->getTitle(),
                'birthDate'  => $command->getBirthDate(),
                'birthPlace' => $command->getBirthPlace()
            ]
        );
        $personResult = $this->handleSideEffect($updatePerson);

        $saveWorkAddress = SaveAddressCmd::create(
            [
                'id'           => $command->getWorkAddressId(),
                'version'      => $command->getWorkAddressVersion(),
                'addressLine1' => $command->getWorkAddressLine1(),
                'addressLine2' => $command->getWorkAddressLine2(),
                'addressLine3' => $command->getWorkAddressLine3(),
                'addressLine4' => $command->getWorkAddressLine4(),
                'town'         => $command->getWorkTown(),
                'postcode'     => $command->getWorkPostcode(),
                'countryCode'  => $command->getWorkCountryCode() ? $command->getWorkCountryCode() : null,
                'contactType'  => ContactDetails::CONTACT_TYPE_TRANSPORT_MANAGER
            ]
        );
        $workAddressResult = $this->handleSideEffect($saveWorkAddress);
        $workCdId = $workAddressResult->getId('contactDetails');

        $saveHomeAddress = SaveAddressCmd::create(
            [
                'id'           => $command->getHomeAddressId(),
                'version'      => $command->getHomeAddressVersion(),
                'addressLine1' => $command->getHomeAddressLine1(),
                'addressLine2' => $command->getHomeAddressLine2(),
                'addressLine3' => $command->getHomeAddressLine3(),
                'addressLine4' => $command->getHomeAddressLine4(),
                'town'         => $command->getHomeTown(),
                'postcode'     => $command->getHomePostcode(),
                'countryCode'  => $command->getHomeCountryCode(),
                'contactType'  => ContactDetails::CONTACT_TYPE_TRANSPORT_MANAGER,
            ]
        );
        $homeAddressResult = $this->handleSideEffect($saveHomeAddress);

        $contactDetails = $this->updateHomeContactDetails($command);
        $transportManager = $this->updateTransportManager($command, $workCdId);
        $this->clearEntityUserCaches($transportManager);

        $result->addId('transportManager', $transportManager->getId());
        $result->addMessage('Transport Manager updated successfully');
        $result->merge($personResult);

        // need to add ids and messages manually, otherewise it will be overwritten
        if ($homeAddressResult->getFlag('hasChanged')) {
            $result->addId('homeAddress', $command->getHomeAddressId());
            $result->addMessage('Home address updated');
        }
        if ($workAddressResult->getFlag('hasChanged')) {
            $result->addId('workAddress', $workAddressResult->getId('address'));
            $result->addMessage('Work address updated');
        }
        if ($command->getHomeCdVersion() !== $contactDetails->getVersion()) {
            $result->addId('homeContactDetails', $contactDetails->getId());
            $result->addMessage('Home contact details updated');
        }

        return $result;
    }

    /**
     * update home contact details
     *
     * @param UpdateTmCmd $command command to update tm
     *
     * @return ContactDetailsEntity
     */
    protected function updateHomeContactDetails($command)
    {
        /** @var ContactDetailsEntity $contactDetails */
        $contactDetails = $this->getRepo('ContactDetails')->fetchById($command->getHomeCdId());
        $contactDetails->updateContactDetailsWithPersonAndEmailAddress(
            null,
            $command->getEmailAddress()
        );
        $this->getRepo('ContactDetails')->save($contactDetails);
        return $contactDetails;
    }

    /**
     * Update a transport manager
     *
     * @param UpdateTmCmd $command  command to update tm
     * @param int|null    $workCdId work contact details id
     *
     * @return TransportManagerEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function updateTransportManager($command, $workCdId = null)
    {
        /** @var TransportManagerEntity $transportManager */
        $transportManager = $this->getRepo('TransportManager')->fetchById($command->getId());

        $transportManager->updateTransportManager(
            $this->getRepo()->getRefdataReference($command->getType()),
            $this->getRepo()->getRefdataReference($command->getStatus()),
            $workCdId ? $this->getRepo()->getReference(ContactDetails::class, $workCdId) : null
        );

        $this->getRepo('TransportManager')->save($transportManager);

        $this->result->merge(
            $this->handleSideEffect(
                $this->nysiisQueueCmd($transportManager->getId())
            )
        );

        return $transportManager;
    }
}
