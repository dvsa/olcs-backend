<?php

/**
 * Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Domain\Repository\OperatingCentre as OcRepo;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;

/**
 * Operating Centre Helper
 *
 * Holds common logic between Licence and Application OC commands
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreHelper
{
    const ERR_OC_AD_IN_1 = 'ERR_OC_AD_IN_1';
    const ERR_OC_AD_DT_1 = 'ERR_OC_AD_DT_1';
    const ERR_OC_VR_1A = 'ERR_OC_VR_1A'; // with trailers
    const ERR_OC_VR_1B = 'ERR_OC_VR_1B'; // without trailers

    protected $messages = [];

    /**
     * @param $entity
     * @param $command
     */
    public function validate($entity, $command)
    {
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
            }
        }

        if (!empty($this->messages)) {
            throw new ValidationException($this->messages);
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
            $ocRepo->getReference(Address::class, $result->getId('address'))
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

    private function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }
}
