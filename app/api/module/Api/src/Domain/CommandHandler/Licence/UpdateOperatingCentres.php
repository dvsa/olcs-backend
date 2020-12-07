<?php

/**
 * Update Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateOperatingCentres as Cmd;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;

/**
 * Update Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateOperatingCentres extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['LicenceOperatingCentre'];

    private $totals;

    /**
     * @var UpdateOperatingCentreHelper
     */
    private $updateHelper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->updateHelper = $serviceLocator->getServiceLocator()->get('UpdateOperatingCentreHelper');

        return parent::createService($serviceLocator);
    }

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($licence, $command);

        if (!$licence->isPsv()) {
            $licence->setTotAuthTrailers($command->getTotAuthTrailers());
        }

        $licence->setTotAuthVehicles($command->getTotAuthVehicles());

        if ($licence->getTrafficArea() !== null) {
            $licence->setEnforcementArea(
                $this->getRepo()->getReference(EnforcementArea::class, $command->getEnforcementArea())
            );
        }

        $this->getRepo()->save($licence);
        $this->result->addMessage('Licence record updated');

        return $this->result;
    }

    protected function validate(Licence $licence, Cmd $command)
    {
        if (!$command->getPartial()) {
            if (!$licence->getOperatingCentres()->isEmpty()) {
                $this->updateHelper->validateEnforcementArea($licence, $command);
            }

            if ($licence->isPsv()) {
                $this->updateHelper->validatePsv($licence, $command);
            } else {
                $this->updateHelper->validateTotalAuthTrailers($command, $this->getTotals($licence));
            }

            $this->updateHelper->validateTotalAuthVehicles($licence, $command, $this->getTotals($licence));
        }

        $messages = $this->updateHelper->getMessages();

        if (!empty($messages)) {
            throw new ValidationException($messages);
        }
    }

    protected function getTotals(Licence $licence)
    {
        if ($this->totals !== null) {
            return $this->totals;
        }

        $locs = $licence->getOperatingCentres();

        $this->totals['noOfOperatingCentres'] = 0;
        $this->totals['minVehicleAuth'] = 0;
        $this->totals['maxVehicleAuth'] = 0;
        $this->totals['minTrailerAuth'] = 0;
        $this->totals['maxTrailerAuth'] = 0;

        /** @var LicenceOperatingCentre $loc */
        foreach ($locs as $loc) {
            $this->totals['noOfOperatingCentres']++;

            $this->totals['minVehicleAuth'] = max([$this->totals['minVehicleAuth'], $loc->getNoOfVehiclesRequired()]);
            $this->totals['minTrailerAuth'] = max([$this->totals['minTrailerAuth'], $loc->getNoOfTrailersRequired()]);

            $this->totals['maxVehicleAuth'] += (int)$loc->getNoOfVehiclesRequired();
            $this->totals['maxTrailerAuth'] += (int)$loc->getNoOfTrailersRequired();
        }

        return $this->totals;
    }
}
