<?php

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
    const ERR_OC_PERMISSION = 'ERR_OC_PERMISSION';
    const ERR_OC_TA_NI_APP = 'ERR_OC_TA_NI_APP';

    /**
     * @var array
     */
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
     * @var \Dvsa\Olcs\Api\Domain\Service\TrafficAreaValidator
     */
    private $trafficAreaValidator;

    /**
     * @var DocRepo
     */
    protected $docRepo;

    /**
     * Create factory for the service
     *
     * @param ServiceLocatorInterface $serviceLocator ZF Service locator
     *
     * @return $this
     *
     * @TODO this needs to be in a factory.  How can a factory be integrated into a service?
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->addressService = $serviceLocator->get('AddressService');
        $this->adminAreaTrafficAreaRepo = $serviceLocator->get('RepositoryServiceManager')->get('AdminAreaTrafficArea');
        $this->trafficAreaValidator = $serviceLocator->get('TrafficAreaValidator');

        $this->docRepo = $serviceLocator->get('RepositoryServiceManager')->get('Document');

        return $this;
    }

    /**
     * Validate entity
     *
     * @param Application|Licence                                    $entity     Entity
     * @param array                                                  $command    Requested parameters from api
     * @param bool                                                   $isExternal Is external?
     * @param LicenceOperatingCentre|ApplicationOperatingCentre|null $xoc        XOC
     *
     * @return void
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
            $this->validateForGoods($entity, $command, $isExternal, $xoc);
        }

        if ($isExternal) {
            $this->validateConfirmations($command->getPermission());
        }

        if (!empty($this->messages)) {
            throw new ValidationException($this->messages);
        }
    }

    /**
     * Validate for goods
     *
     * @param Application|Licence                                    $entity     Entity
     * @param array                                                  $command    Requested parameters from api
     * @param bool                                                   $isExternal Is external?
     * @param LicenceOperatingCentre|ApplicationOperatingCentre|null $xoc        XOC
     *
     * @return void
     */
    protected function validateForGoods($entity, $command, $isExternal = false, $xoc = null)
    {
        $sum = (int)$command->getNoOfVehiclesRequired() + (int)$command->getNoOfTrailersRequired();
        if ($sum < 1) {
            $this->addMessage('noOfVehiclesRequired', self::ERR_OC_VR_1A);
            $this->addMessage('noOfTrailersRequired', self::ERR_OC_VR_1A);
        }

        if ($command->getAdPlaced() === ApplicationOperatingCentre::AD_UPLOAD_NOW) {
            if ((string)$command->getAdPlacedIn() === '') {
                $this->addMessage('adPlacedIn', self::ERR_OC_AD_IN_1);
            }

            if ((string)$command->getAdPlacedDate() === '') {
                $this->addMessage('adPlacedDate', self::ERR_OC_AD_DT_1);
            }

            if ($isExternal) {
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
    }

    /**
     * Validate traffic area
     *
     * @param Application|Licence $entity  Application or Licence entity
     * @param array               $command Command from API
     *
     * @return void
     */
    public function validateTrafficArea($entity, $command)
    {
        $address = $command->getAddress();

        // If we have no postcode, then we can skip this validation
        if (empty($address['postcode'])) {
            return;
        }

        try {
            $trafficArea = $this->fetchTrafficAreaByPostcode($address['postcode']);
        } catch (\Exception $e) {
            // If address service is not available then we can skip validation
            return;
        }

        // If we can't match the postcode to a TA, then we can skip
        if ($trafficArea === null) {
            return;
        }

        // if new application check not other application/licences in this traffic area
        if ($entity instanceof Application && $entity->isNew()) {
            if (
                $trafficArea->getId() === TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
                && $entity->getNiFlag() === 'N'
            ) {
                $this->addMessage('postcode', self::ERR_OC_TA_NI_APP);
                return;
            }
            // validate
            $message = $this->trafficAreaValidator->validateForSameTrafficAreas($entity, $trafficArea->getId());
            if (is_array($message)) {
                $this->addMessage(
                    'postcode',
                    key($message),
                    current($message)
                );
            }
        }

        // If we are GB and don't have a TA then we can skip
        if ($entity->getNiFlag() === 'N' && $entity->getTrafficArea() === null) {
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

        //if not overridden
        if($command->getTaIsOveridden() === "N") {
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
    }

    /**
     * Save documents
     *
     * @param Licence|Application $entity          Licence or Application entity
     * @param OperatingCentre     $operatingCentre operating Centre entity
     * @param DocumentRepo        $documentRepo    Document Repository
     *
     * @return void
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
     * Create operating centre
     *
     * @param array                 $command        API response
     * @param CommandHandlerManager $commandHandler Command handler manager
     * @param Result                $result         Command result
     * @param OcRepo                $ocRepo         Operating Centre repository
     *
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

        $result->merge($commandHandler->handleCommand(SaveAddress::create($data), false));

        $operatingCentre = new OperatingCentre();

        $operatingCentre->setAddress(
            $ocRepo->getReference(AddressEntity::class, $result->getId('address'))
        );

        $ocRepo->save($operatingCentre);

        return $operatingCentre;
    }

    /**
     * Update Operating Centre
     *
     * @param ApplicationOperatingCentre|LicenceOperatingCentre $ocLink  OC link
     * @param Application|Licence                               $entity  Entity
     * @param array                                             $command Command
     * @param OcRepo                                            $repo    Operating Centre Repository
     *
     * @return void
     */
    public function updateOperatingCentreLink(
        $ocLink,
        $entity,
        $command,
        $repo
    ) {
        $ocLink->setNoOfVehiclesRequired($command->getNoOfVehiclesRequired());
        $ocLink->setPermission($command->getPermission());

        if ($entity->isPsv()) {
            $ocLink->setAdPlaced(ApplicationOperatingCentre::AD_POST);
        } else {
            $ocLink->setAdPlaced((int) $command->getAdPlaced());
            if ((int) $command->getAdPlaced() === ApplicationOperatingCentre::AD_UPLOAD_NOW) {
                $ocLink->setAdPlacedIn($command->getAdPlacedIn());
                $ocLink->setAdPlacedDate(new DateTime($command->getAdPlacedDate()));
            }

            $ocLink->setNoOfTrailersRequired($command->getNoOfTrailersRequired());
        }

        $repo->save($ocLink);
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add message
     *
     * @param string      $field       Field
     * @param string      $messageCode Message code
     * @param null|string $message     Message
     *
     * @return void
     */
    private function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }

    /**
     * Fetch traffic area using postcode
     *
     * @param string $postcode postcode service
     *
     * @return \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea
     */
    private function fetchTrafficAreaByPostcode($postcode)
    {
        return $this->addressService->fetchTrafficAreaByPostcode(
            $postcode,
            $this->adminAreaTrafficAreaRepo
        );
    }

    /**
     * Validate confirmation
     *
     * @param string $permission Permission Y or N
     *
     * @return void
     */
    public function validateConfirmations($permission)
    {
        if ($permission !== 'Y') {
            $this->addMessage('permission', self::ERR_OC_PERMISSION);
        }
    }
}
