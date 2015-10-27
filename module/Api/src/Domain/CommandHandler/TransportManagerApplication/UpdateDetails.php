<?php

/**
 * UpdateDetails
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\UpdateDetails as UpdateDetailsCommand;
use Doctrine\ORM\Query;

/**
 * UpdateDetails
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateDetails extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerApplication';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command UpdateDetailsCommand */

        $result = new Result();

        /* @var $tma TransportManagerApplication */
        $tma = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->updateTma($tma, $command);
        $this->updateHomeAddress($tma, $command->getHomeAddress());
        $this->updateWorkAddress($tma, $command->getWorkAddress());
        $tma->getTransportManager()->getHomeCd()->setEmailAddress($command->getEmail());
        $tma->getTransportManager()->getHomeCd()->getPerson()->setBirthPlace($command->getPlaceOfBirth());

        if ($command->getSubmit() === 'Y') {
            // could validate the TMA here?
            $tma->setDeclarationConfirmation('Y');
        }

        $this->getRepo()->save($tma);

        $result->addMessage("Transport Manager Application ID {$tma->getId()} updated");

        return $result;
    }

    /**
     * Update TMA properties
     *
     * @param TransportManagerApplication $tma
     * @param UpdateDetailsCommand        $command
     */
    protected function updateTma(TransportManagerApplication $tma, UpdateDetailsCommand $command)
    {
        if ($command->getTmType()) {
            $tma->setTmType($this->getRepo()->getRefdataReference($command->getTmType()));
        }
        $tma->setAdditionalInformation($command->getAdditionalInfo());
        $tma->setHoursMon((float) $command->getHoursMon());
        $tma->setHoursTue((float) $command->getHoursTue());
        $tma->setHoursWed((float) $command->getHoursWed());
        $tma->setHoursThu((float) $command->getHoursThu());
        $tma->setHoursFri((float) $command->getHoursFri());
        $tma->setHoursSat((float) $command->getHoursSat());
        $tma->setHoursSun((float) $command->getHoursSun());
        if ($command->getIsOwner()) {
            $tma->setIsOwner($command->getIsOwner());
        }

        $tma->getOperatingCentres()->clear();
        foreach ($command->getOperatingCentres() as $ocId) {
            $tma->getOperatingCentres()->add(
                $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre::class, $ocId)
            );
        }
    }

    /**
     * Update home address
     *
     * @param TransportManagerApplication $tma
     * @param array                       $address
     */
    protected function updateHomeAddress(TransportManagerApplication $tma, array $address)
    {
        $this->populateAddress($tma->getTransportManager()->getHomeCd()->getAddress(), $address);
    }

    /**
     * Update work address
     *
     * @param TransportManagerApplication $tma
     * @param array                       $address
     */
    protected function updateWorkAddress(TransportManagerApplication $tma, array $address)
    {
        if (!$tma->getTransportManager()->getWorkCd()) {
            $tma->getTransportManager()->setWorkCd(
                new ContactDetails(
                    $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_TRANSPORT_MANAGER)
                )
            );
        }
        if (!$tma->getTransportManager()->getWorkCd()->getAddress()) {
            $tma->getTransportManager()->getWorkCd()->setAddress(new \Dvsa\Olcs\Api\Entity\ContactDetails\Address());
        }

        $countryCode = null;
        if (!empty($address['countryCode'])) {
            $countryCode = $this->getRepo()->getReference(Country::class, $address['countryCode']);
        }

        $this->populateAddress($tma->getTransportManager()->getWorkCd()->getAddress(), $address);

        $this->getRepo('ContactDetails')->save($tma->getTransportManager()->getWorkCd());
    }

    /**
     * Populate and Address entity
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\Address $address
     * @param array $addressData
     */
    protected function populateAddress(\Dvsa\Olcs\Api\Entity\ContactDetails\Address $address, array $addressData)
    {
        $countryCode = null;
        if (!empty($addressData['countryCode'])) {
            $countryCode = $this->getRepo()->getReference(Country::class, $addressData['countryCode']);
        }

        $address->updateAddress(
            $addressData['addressLine1'],
            $addressData['addressLine2'],
            $addressData['addressLine3'],
            $addressData['addressLine4'],
            $addressData['town'],
            $addressData['postcode'],
            $countryCode
        );
    }
}
