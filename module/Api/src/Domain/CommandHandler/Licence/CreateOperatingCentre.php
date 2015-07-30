<?php

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\CreateOperatingCentre as Cmd;

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateOperatingCentre extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    const ERR_OC_AD_IN_1 = 'ERR_OC_AD_IN_1';
    const ERR_OC_AD_DT_1 = 'ERR_OC_AD_DT_1';
    const ERR_OC_VR_1A = 'ERR_OC_VR_1A'; // with trailers
    const ERR_OC_VR_1B = 'ERR_OC_VR_1B'; // without trailers

    protected $repoServiceName = 'Licence';

    protected $extraRepos = [
        'Document',
        'OperatingCentre',
        'LicenceOperatingCentre'
    ];

    private $messages = [];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::INTERNAL_USER)) {
            throw new ForbiddenException();
        }

        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        $this->validate($licence, $command);

        // Create an OC record
        $operatingCentre = $this->createOperatingCentre($command);

        // Link, unlinked documents to the OC
        $this->saveDocuments($licence, $operatingCentre);

        // Create a AOC record
        $this->createLicenceOperatingCentre($licence, $operatingCentre, $command);

        return $this->result;
    }

    private function validate(Licence $licence, Cmd $command)
    {
        if ($licence->isPsv() && (int)$command->getNoOfVehiclesRequired() < 1) {
            $this->addMessage('noOfVehiclesRequired', self::ERR_OC_VR_1B);
        }

        if ($licence->isGoods()) {
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
     * @param Licence $licence
     * @param OperatingCentre $operatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createLicenceOperatingCentre(
        Licence $licence,
        OperatingCentre $operatingCentre,
        Cmd $command
    ) {
        $loc = new LicenceOperatingCentre($licence, $operatingCentre);

        $loc->setNoOfVehiclesRequired($command->getNoOfVehiclesRequired());
        $loc->setPermission($command->getPermission());
        $loc->setSufficientParking($command->getSufficientParking());

        if ($licence->isPsv()) {
            $loc->setAdPlaced(false);
        } else {
            $loc->setAdPlaced($command->getAdPlaced());
            if ($command->getAdPlaced() === 'Y') {
                $loc->setAdPlacedIn($command->getAdPlacedIn());
                $loc->setAdPlacedDate(new DateTime($command->getAdPlacedDate()));
            }

            $loc->setNoOfTrailersRequired($command->getNoOfTrailersRequired());
        }

        $licence->addOperatingCentres($loc);

        $this->getRepo('LicenceOperatingCentre')->save($loc);
    }

    /**
     * @param Licence $licence
     * @param OperatingCentre $operatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function saveDocuments(Licence $licence, OperatingCentre $operatingCentre)
    {
        $documents = $this->getRepo('Document')->fetchUnlinkedOcDocumentsForEntity($licence);

        /** @var Document $document */
        foreach ($documents as $document) {
            $document->setOperatingCentre($operatingCentre);
            $this->getRepo('Document')->save($document);
        }
    }

    private function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }
}
