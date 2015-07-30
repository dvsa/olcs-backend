<?php

/**
 * Update Application Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationOperatingCentre;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\ApplicationOperatingCentre\Update as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Doc\Document;

/**
 * Update Application Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    const ERR_OC_AD_IN_1 = 'ERR_OC_AD_IN_1';
    const ERR_OC_AD_DT_1 = 'ERR_OC_AD_DT_1';
    const ERR_OC_VR_1A = 'ERR_OC_VR_1A'; // with trailers
    const ERR_OC_VR_1B = 'ERR_OC_VR_1B'; // without trailers

    protected $repoServiceName = 'ApplicationOperatingCentre';

    protected $extraRepos = ['Document', 'OperatingCentre',];

    private $messages = [];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationOperatingCentre $aoc */
        $aoc = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $application = $aoc->getApplication();

        $this->validate($application, $command);

        $operatingCentre = $aoc->getOperatingCentre();

        if ($command->getAddress() !== null) {
            $data = $command->getAddress();
            $this->result->merge($this->handleSideEffect(SaveAddress::create($data)));
        }

        // Link, unlinked documents to the OC
        $this->saveDocuments($application, $operatingCentre);

        $this->updateApplicationOperatingCentre($aoc, $application, $command);

        $completionData = ['id' => $application->getId(), 'section' => 'operatingCentres'];
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
     * @param ApplicationOperatingCentre $aoc
     * @param Cmd $command
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function updateApplicationOperatingCentre(
        ApplicationOperatingCentre $aoc,
        Application $application,
        Cmd $command
    ) {
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

    private function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }
}
