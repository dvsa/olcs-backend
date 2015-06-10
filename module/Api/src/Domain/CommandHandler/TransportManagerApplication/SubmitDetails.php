<?php

/**
 * SubmitDetails
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
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\SubmitDetails as SubmitDetailsCommand;
use Doctrine\ORM\Query;

/**
 * SubmitDetails
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class SubmitDetails extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerApplication';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command SubmitDetailsCommand */

        $result = new Result();

        /* @var $tma TransportManagerApplication */
        $tma = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $this->updateTma($tma, $command);
        $homeCdId = $this->updateAddress($command->getHomeAddress());
        if ($homeCdId) {
            $tma->getTransportManager()->setHomeCd($this->getRepo()->getReference(ContactDetails::class, $homeCdId));
        }
        $workCdId = $this->updateAddress($command->getWorkAddress());
        if ($workCdId) {
            $tma->getTransportManager()->setWorkCd($this->getRepo()->getReference(ContactDetails::class, $workCdId));
        }
        $tma->getTransportManager()->getHomeCd()->setEmailAddress($command->getEmail());
        $tma->getTransportManager()->getHomeCd()->getPerson()->setBirthPlace($command->getPlaceOfBirth());

        $this->getRepo()->save($tma);

        $result->addMessage("Transport Manager ID {$tma->getId()} updated");

        return $result;
    }

    /**
     * Update TMA properties
     * 
     * @param TransportManagerApplication $tma
     * @param SubmitDetailsCommand        $command
     */
    protected function updateTma(TransportManagerApplication $tma, SubmitDetailsCommand $command)
    {
        $tma->setTmType($this->getRepo()->getRefdataReference($command->getTmType()));
        $tma->setAdditionalInformation($command->getAdditionalInfo());
        $tma->setHoursMon((int) $command->getHoursMon());
        $tma->setHoursTue((int) $command->getHoursTue());
        $tma->setHoursWed((int) $command->getHoursWed());
        $tma->setHoursThu((int) $command->getHoursThu());
        $tma->setHoursFri((int) $command->getHoursFri());
        $tma->setHoursSat((int) $command->getHoursSat());
        $tma->setHoursSun((int) $command->getHoursSun());
        $tma->setIsOwner($command->getIsOwner());
        $tma->setDeclarationConfirmation('Y');

        $tma->getOperatingCentres()->clear();
        foreach ($command->getOperatingCentres() as $ocId) {
            $tma->getOperatingCentres()->add(
                $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre::class, $ocId)
            );
        }

        $tma->setTmApplicationStatus(
            $this->getRepo()->getRefdataReference(TransportManagerApplication::STATUS_AWAITING_SIGNATURE)
        );
    }

    /**
     * Update/Create an Address
     *
     * @param array $address
     * 
     * @return int|null ContactDetails ID if created, otherwise null
     */
    protected function updateAddress($address)
    {
        $response = $this->getCommandHandler()->handleCommand(
            \Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress::create(
                [
                    'id' => $address['id'],
                    'version' => $address['version'],
                    'addressLine1' => $address['addressLine1'],
                    'addressLine2' => $address['addressLine2'],
                    'addressLine3' => $address['addressLine3'],
                    'addressLine4' => $address['addressLine4'],
                    'town' => $address['town'],
                    'postcode' => $address['postcode'],
                    'countryCode' => $address['countryCode'],
                    'contactType' => ContactDetails::CONTACT_TYPE_TRANSPORT_MANAGER,
                ]
            )
        );

        return $response->getId('contactDetails');
    }
}
