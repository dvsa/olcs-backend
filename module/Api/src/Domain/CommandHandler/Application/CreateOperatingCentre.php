<?php

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Address\Service\AddressServiceAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\CreateOperatingCentre as Cmd;

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateOperatingCentre extends AbstractCommandHandler implements
    TransactionedInterface,
    AddressServiceAwareInterface
{
    const ERR_OC_AD_IN_1 = 'ERR_OC_AD_IN_1';
    const ERR_OC_AD_DT_1 = 'ERR_OC_AD_DT_1';
    const ERR_OC_VR_1A = 'ERR_OC_VR_1A'; // with trailers
    const ERR_OC_VR_1B = 'ERR_OC_VR_1B'; // without trailers

    use AddressServiceAwareTrait;

    protected $repoServiceName = 'Application';

    protected $extraRepos = [
        'Document',
        'OperatingCentre',
        'ApplicationOperatingCentre',
        'AdminAreaTrafficArea',
        'PostcodeEnforcementArea'
    ];

    private $messages = [];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        $this->validate($application, $command);

        // Create an OC record
        $operatingCentre = $this->createOperatingCentre($command);

        // Link, unlinked documents to the OC
        $this->saveDocuments($application, $operatingCentre);

        // Create a AOC record
        $this->createApplicationOperatingCentre($application, $operatingCentre, $command);

        $this->setDefaultTrafficAreaAndEnforcementArea($application, $operatingCentre);

        $completionData = ['id' => $command->getApplication(), 'section' => 'operatingCentres'];
        $this->result->merge($this->handleSideEffect(UpdateApplicationCompletionCmd::create($completionData)));

        return $this->result;
    }

    private function validate(Application $application, Cmd $command)
    {
        if ($application->isPsv() && (int)$command->getNoOfVehiclesRequired() < 1) {
            $this->addMessage('noOfVehiclesRequired', self::ERR_OC_VR_1B);
        }

        if ($application->isGoods()) {
            $sum = (int)$command->getNoOfVehiclesRequired() + (int)$command->getNoOfTrailersRequired();
            if ($sum < 1) {
                $this->addMessage('noOfVehiclesRequired', self::ERR_OC_VR_1A);
                $this->addMessage('noOfTrailersRequired', self::ERR_OC_VR_1A);
            }

            if ($command->getAdPlaced() === 'Y') {
                if ((string)$command->getAdPlacedIn() === '') {
                    $this->addMessage('adPlacedIn', self::ERR_OC_AD_IN_1);
                }

                if ((string)$command->getAdPlacedDate() === '') {
                    $this->addMessage('adPlacedDate', self::ERR_OC_AD_DT_1);
                }
            }
        }

        if (!empty($this->messages)) {
            throw new ValidationException($this->messages);
        }
    }

    /**
     * @param Cmd $command
     * @return OperatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createOperatingCentre(Cmd $command)
    {
        $data = $command->getAddress();
        $data['contactType'] = ContactDetails::CONTACT_TYPE_CORRESPONDENCE_ADDRESS;

        $this->result->merge($this->handleSideEffect(SaveAddress::create($data)));

        $operatingCentre = new OperatingCentre();

        $operatingCentre->setAddress(
            $this->getRepo()->getReference(Address::class, $this->result->getId('address'))
        );

        $this->getRepo('OperatingCentre')->save($operatingCentre);

        return $operatingCentre;
    }

    /**
     * @param Application $application
     * @param OperatingCentre $operatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createApplicationOperatingCentre(
        Application $application,
        OperatingCentre $operatingCentre,
        Cmd $command
    ) {
        $aoc = new ApplicationOperatingCentre($application, $operatingCentre);
        $aoc->setAction('A');

        $aoc->setNoOfVehiclesRequired($command->getNoOfVehiclesRequired());
        $aoc->setPermission($command->getPermission());
        $aoc->setSufficientParking($command->getSufficientParking());

        if ($application->isPsv()) {
            $aoc->setAdPlaced(false);
        } else {
            $aoc->setAdPlaced($command->getAdPlaced());
            if ($command->getAdPlaced() === 'Y') {
                $aoc->setAdPlacedIn($command->getAdPlacedIn());
                $aoc->setAdPlacedDate(new DateTime($command->getAdPlacedDate()));
            }

            $aoc->setNoOfTrailersRequired($command->getNoOfTrailersRequired());
        }

        $application->addOperatingCentres($aoc);

        $this->getRepo('ApplicationOperatingCentre')->save($aoc);
    }

    /**
     * @param Application $application
     * @param OperatingCentre $operatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function saveDocuments(Application $application, OperatingCentre $operatingCentre)
    {
        $documents = $this->getRepo('Document')->fetchUnlinkedOcDocumentsForEntity($application);

        /** @var Document $document */
        foreach ($documents as $document) {
            $document->setOperatingCentre($operatingCentre);
            $this->getRepo('Document')->save($document);
        }
    }

    private function setDefaultTrafficAreaAndEnforcementArea(Application $application, OperatingCentre $operatingCentre)
    {
        $setEa = $setTa = false;

        if ($application->getLicence()->getTrafficArea() === null) {
            $setTa = true;
        }

        if ($application->getLicence()->getEnforcementArea() === null) {
            $setEa = true;
        }

        if ($setEa === false && $setTa === false) {
            return;
        }

        if ($application->getNiFlag() === 'Y') {
            if ($setTa) {
                $application->getLicence()->setTrafficArea(
                    $this->getRepo()->getReference(TrafficArea::class, TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE)
                );
            }

            if ($setEa) {
                $application->getLicence()->setEnforcementArea(
                    $this->getRepo()->getReference(
                        EnforcementArea::class,
                        EnforcementArea::NORTHERN_IRELAND_ENFORCEMENT_AREA_CODE
                    )
                );
            }
        } else {

            $postcode = $operatingCentre->getAddress()->getPostcode();

            if (!empty($postcode) && $application->getOperatingCentres()->count() === 1) {

                if ($setTa) {
                    $trafficArea = $this->getAddressService()
                        ->fetchTrafficAreaByPostcode($postcode, $this->getRepo('AdminAreaTrafficArea'));

                    $application->getLicence()->setTrafficArea($trafficArea);
                }

                if ($setEa) {
                    $enforcementArea = $this->getAddressService()
                        ->fetchEnforcementAreaByPostcode($postcode, $this->getRepo('PostcodeEnforcementArea'));

                    $application->getLicence()->setEnforcementArea($enforcementArea);
                }
            }
        }

        $this->getRepo()->save($application);
    }

    private function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }
}
