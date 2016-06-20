<?php

/**
 * Transport Manager / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as SaveAddressCmd;
use Dvsa\Olcs\Api\Domain\Command\Person\UpdateFull as UpdatePersonCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Tm\UpdateNysiisName as UpdateNysiisNameCmd;
use Zend\Serializer\Adapter\Json as ZendJson;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueue;

/**
 * Transport Manager / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManager';

    protected $extraRepos = ['ContactDetails'];

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

    protected function updateHomeContactDetails($command)
    {
        $contactDetails = $this->getRepo('ContactDetails')->fetchById($command->getHomeCdId());
        $contactDetails->updateContactDetailsWithPersonAndEmailAddress(
            null,
            $command->getEmailAddress()
        );
        $this->getRepo('ContactDetails')->save($contactDetails);
        return $contactDetails;
    }

    /**
     * @param $command
     * @param null $workCdId
     * @return mixed
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function updateTransportManager($command, $workCdId = null)
    {
        $transportManager = $this->getRepo('TransportManager')->fetchById($command->getId());

        $transportManager->updateTransportManager(
            $this->getRepo()->getRefdataReference($command->getType()),
            $this->getRepo()->getRefdataReference($command->getStatus()),
            $workCdId ? $this->getRepo()->getReference(ContactDetails::class, $workCdId) : null
        );

        $this->getRepo('TransportManager')->save($transportManager);

        $this->handleSideEffects(
            $this->getNysiisNameUpdateQueueCmd(
                [
                    'id' => $transportManager->getId()
                ]
            )
        );

        return $transportManager;
    }

    /**
     * Returns a command to queue a NYSIIS name request and update
     *
     * @param array $params
     * @return UpdateNysiisNameCmd
     */
    private function getNysiisNameUpdateQueueCmd($params)
    {
        $jsonSerializer = new ZendJson();

        $optionData = [
            'id' => $params['id']
        ];

        $dtoData = [
            'entityId' => $params['id'],
            'type' => Queue::TYPE_UPDATE_NYSIIS_TM_NAME,
            'status' => Queue::STATUS_QUEUED,
            'options' => $jsonSerializer->serialize($optionData)
        ];

        return CreateQueue::create($dtoData);
    }
}
