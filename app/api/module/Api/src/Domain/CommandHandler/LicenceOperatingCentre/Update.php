<?php

/**
 * Update Licence Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceOperatingCentre;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\LicenceOperatingCentre\Update as Cmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Doc\Document;

/**
 * Update Licence Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    const ERR_OC_AD_IN_1 = 'ERR_OC_AD_IN_1';
    const ERR_OC_AD_DT_1 = 'ERR_OC_AD_DT_1';
    const ERR_OC_VR_1A = 'ERR_OC_VR_1A'; // with trailers
    const ERR_OC_VR_1B = 'ERR_OC_VR_1B'; // without trailers

    protected $repoServiceName = 'LicenceOperatingCentre';

    protected $extraRepos = ['Document', 'OperatingCentre',];

    private $messages = [];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var LicenceOperatingCentre $loc */
        $loc = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $licence = $loc->getLicence();

        $this->validate($licence, $command);

        $operatingCentre = $loc->getOperatingCentre();

        $data = $command->getAddress();
        $this->result->merge($this->handleSideEffect(SaveAddress::create($data)));

        // Link, unlinked documents to the OC
        $this->saveDocuments($licence, $operatingCentre);

        $this->updateLicenceOperatingCentre($loc, $licence, $command);

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
     * @param LicenceOperatingCentre $loc
     * @param Cmd $command
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function updateLicenceOperatingCentre(
        LicenceOperatingCentre $loc,
        Licence $licence,
        Cmd $command
    ) {
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
