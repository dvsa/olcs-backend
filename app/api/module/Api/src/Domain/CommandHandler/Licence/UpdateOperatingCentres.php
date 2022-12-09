<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
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
 * @see \Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence\UpdateOperatingCentresTest
 * @see \Dvsa\Olcs\Transfer\Command\Licence\UpdateOperatingCentres
 */
final class UpdateOperatingCentres extends AbstractCommandHandler implements TransactionedInterface, CacheAwareInterface
{
    use CacheAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['LicenceOperatingCentre'];

    private $totals;

    /**
     * @var UpdateOperatingCentreHelper
     */
    private $updateHelper;

    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $this->updateHelper = $serviceLocator->getServiceLocator()->get('UpdateOperatingCentreHelper');

        return parent::createService($serviceLocator);
    }

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        assert($command instanceof Cmd);

        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        assert($licence instanceof Licence);

        $this->validate($licence, $command);

        if (! $licence->isPsv()) {
            $licence->setTotAuthTrailers($command->getTotAuthTrailers());
            $licence->updateTotAuthLgvVehicles($command->getTotAuthLgvVehicles());
        }

        $licence->updateTotAuthHgvVehicles($command->getTotAuthHgvVehicles());

        if ($licence->getTrafficArea() !== null) {
            $licence->setEnforcementArea(
                $this->getRepo()->getReference(EnforcementArea::class, $command->getEnforcementArea())
            );
        }

        $this->getRepo()->save($licence);
        $this->clearLicenceCaches($licence);
        $this->result->addMessage('Licence record updated');

        return $this->result;
    }

    protected function validate(Licence $licence, Cmd $command)
    {
        if (!$command->getPartial()) {
            if (!$licence->getOperatingCentres()->isEmpty()) {
                $this->updateHelper->validateEnforcementArea($licence, $command);
            }
            $totals = $this->getTotals($licence);

            if ($licence->isPsv()) {
                $this->updateHelper->validatePsv($licence, $command);
            } else {
                $this->updateHelper->validateTotalAuthTrailers($licence, $command, $totals);
                $this->updateHelper->validateTotalAuthLgvVehicles($licence, $command);
            }

            $this->updateHelper->validateTotalAuthHgvVehicles($licence, $command, $totals);
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
        $this->totals['minHgvVehicleAuth'] = 0;
        $this->totals['maxHgvVehicleAuth'] = 0;
        $this->totals['minTrailerAuth'] = 0;
        $this->totals['maxTrailerAuth'] = 0;

        foreach ($locs as $loc) {
            assert($loc instanceof LicenceOperatingCentre);
            $this->totals['noOfOperatingCentres']++;

            $this->totals['minHgvVehicleAuth'] = max([$this->totals['minHgvVehicleAuth'], $loc->getNoOfVehiclesRequired()]);
            $this->totals['minTrailerAuth'] = max([$this->totals['minTrailerAuth'], $loc->getNoOfTrailersRequired()]);

            $this->totals['maxHgvVehicleAuth'] += (int) $loc->getNoOfVehiclesRequired();
            $this->totals['maxTrailerAuth'] += (int) $loc->getNoOfTrailersRequired();
        }

        return $this->totals;
    }
}
