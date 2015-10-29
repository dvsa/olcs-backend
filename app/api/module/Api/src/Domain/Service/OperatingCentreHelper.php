<?php

/**
 * Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Address\Service\Address;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\AdminAreaTrafficArea;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Domain\Repository\OperatingCentre as OcRepo;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocRepo;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;

/**
 * Operating Centre Helper
 *
 * Holds common logic between Licence and Application OC commands
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreHelper implements FactoryInterface
{
    const ERR_OC_AD_IN_1 = 'ERR_OC_AD_IN_1';
    const ERR_OC_AD_DT_1 = 'ERR_OC_AD_DT_1';
    const ERR_OC_AD_FI_1 = 'ERR_OC_AD_FI_1';
    const ERR_OC_VR_1A = 'ERR_OC_VR_1A'; // with trailers
    const ERR_OC_VR_1B = 'ERR_OC_VR_1B'; // without trailers
    const ERR_OR_R_TOO_MANY = 'ERR_OR_R_TOO_MANY';
    const ERR_OC_PC_TA_NI = 'ERR_OC_PC_TA_NI';
    const ERR_OC_PC_TA_GB = 'ERR_OC_PC_TA_GB';
    const ERR_OC_SUFFICIENT_PARKING = 'ERR_OC_SUFFICIENT_PARKING';
    const ERR_OC_PERMISSION = 'ERR_OC_PERMISSION';

    protected $messages = [];

    /**
     * @var Address
     */
    protected $addressService;

    /**
     * @var AdminAreaTrafficArea
     */
    protected $adminAreaTrafficAreaRepo;

    /**
     * @var DocRepo
     */
    protected $docRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->addressService = $serviceLocator->get('AddressService');
        $this->adminAreaTrafficAreaRepo = $serviceLocator->get('RepositoryServiceManager')->get('AdminAreaTrafficArea');

        $this->docRepo = $serviceLocator->get('RepositoryServiceManager')->get('Document');

        return $this;
    }

    /**
     * @param Application|Licence $entity
     * @param $command
     * @param bool $isExternal
     * @param LicenceOperatingCentre|ApplicationOperatingCentre|null $xoc
     */
    public function validate($entity, $command, $isExternal = false, $xoc = null)
    {
        $this->validateTrafficArea($entity, $command);

        if ($entity->isPsv() && $entity->isRestricted() && (int)$command->getNoOfVehiclesRequired() > 2) {
            $this->addMessage('noOfVehiclesRequired', self::ERR_OR_R_TOO_MANY);
        }

        if ($entity->isPsv() && (int)$command->getNoOfVehiclesRequired() < 1) {
            $this->addMessage('noOfVehiclesRequired', self::ERR_OC_VR_1B);
        }

        if ($entity->isGoods()) {
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

                if ($xoc !== null) {
                    $documents = $xoc->getOperatingCentre()->getAdDocuments();
                } else {
                    $documents = $this->docRepo->fetchUnlinkedOcDocumentsForEntity($entity);
                }

                if ($documents->isEmpty()) {
                    $this->addMessage('file', self::ERR_OC_AD_FI_1);
                }
            }
        }

        if ($isExternal) {
            $this->validateConfirmations($command->getSufficientParking(), $command->getPermission());
        }

        if (!empty($this->messages)) {
            throw new ValidationException($this->messages);
        }
    }

    /**
     * @param Application|Licence $entity
     * @param $command
     */
    public function validateTrafficArea($entity, $command)
    {
        $address = $command->getAddress();

        // If we have no postcode, then we can skip this validation
        if (empty($address['postcode'])) {
            return;
        }

        // If we are GB and don't have a TA then we can skip
        if ($entity->getNiFlag() === 'N' && $entity->getTrafficArea() === null) {
            return;
        }

        $trafficArea = $this->fetchTrafficAreaByPostcode($address['postcode']);

        // If we can't match the postcode to a TA, then we can skip
        if ($trafficArea === null) {
            return;
        }

        // If we are NI, then we must match the NI TA
        if ($entity->getNiFlag() === 'Y') {

            if ($trafficArea->getId() !== TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                $this->addMessage('postcode', self::ERR_OC_PC_TA_NI);
            }
            return;
        }

        $currentTa = $entity->getTrafficArea();

        if ($trafficArea !== $currentTa) {
            $this->addMessage(
                'postcode',
                self::ERR_OC_PC_TA_GB,
                json_encode(
                    [
                        'current' => $currentTa->getName(),
                        'oc' => $trafficArea->getName()
                    ]
                )
            );
        }
    }

    /**
     * @param Licence|Application $entity
     * @param OperatingCentre $operatingCentre
     * @param DocumentRepo $documentRepo
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function saveDocuments($entity, OperatingCentre $operatingCentre, DocumentRepo $documentRepo)
    {
        $documents = $documentRepo->fetchUnlinkedOcDocumentsForEntity($entity);

        /** @var Document $document */
        foreach ($documents as $document) {
            $document->setOperatingCentre($operatingCentre);
            $documentRepo->save($document);
        }
    }

    /**
     * @param $command
     * @param CommandHandlerManager $commandHandler
     * @param Result $result
     * @param OcRepo $ocRepo
     * @return OperatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function createOperatingCentre(
        $command,
        CommandHandlerManager $commandHandler,
        Result $result,
        OcRepo $ocRepo
    ) {
        $data = $command->getAddress();
        $data['contactType'] = ContactDetails::CONTACT_TYPE_CORRESPONDENCE_ADDRESS;

        $result->merge($commandHandler->handleCommand(SaveAddress::create($data)));

        $operatingCentre = new OperatingCentre();

        $operatingCentre->setAddress(
            $ocRepo->getReference(AddressEntity::class, $result->getId('address'))
        );

        $ocRepo->save($operatingCentre);

        return $operatingCentre;
    }

    /**
     * @param ApplicationOperatingCentre|LicenceOperatingCentre $ocLink
     * @param Application|Licence $entity
     * @param $command
     * @param $repo
     */
    public function updateOperatingCentreLink(
        $ocLink,
        $entity,
        $command,
        $repo
    ) {
        $ocLink->setNoOfVehiclesRequired($command->getNoOfVehiclesRequired());
        $ocLink->setPermission($command->getPermission());
        $ocLink->setSufficientParking($command->getSufficientParking());

        if ($entity->isPsv()) {
            $ocLink->setAdPlaced(false);
        } else {
            $ocLink->setAdPlaced($command->getAdPlaced());
            if ($command->getAdPlaced() === 'Y') {
                $ocLink->setAdPlacedIn($command->getAdPlacedIn());
                $ocLink->setAdPlacedDate(new DateTime($command->getAdPlacedDate()));
            }

            $ocLink->setNoOfTrailersRequired($command->getNoOfTrailersRequired());
        }

        $repo->save($ocLink);
    }

    public function getMessages()
    {
        return $this->messages;
    }

    private function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }

    /**
     * @param $postcode
     * @return \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea
     */
    private function fetchTrafficAreaByPostcode($postcode)
    {
        return $this->addressService->fetchTrafficAreaByPostcode(
            $postcode,
            $this->adminAreaTrafficAreaRepo
        );
    }

    public function validateConfirmations($sufficientParking, $permission)
    {
        if ($sufficientParking !== 'Y') {
            $this->addMessage('sufficientParking', self::ERR_OC_SUFFICIENT_PARKING);
        }
        if ($permission !== 'Y') {
            $this->addMessage('permission', self::ERR_OC_PERMISSION);
        }
    }
}
