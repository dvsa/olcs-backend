<?php

/**
 * Process Duplicate Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity;

/**
 * Process Duplicate Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ProcessDuplicateVehicles extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['LicenceVehicle'];

    public function handleCommand(CommandInterface $command)
    {
        /** @var Entity\Application\Application $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $licence = $application->getLicence();

        $licenceVehicles = $application->getLicenceVehicles();

        $count = 0;

        /** @var Entity\Licence\LicenceVehicle $licenceVehicle */
        foreach ($licenceVehicles as $licenceVehicle) {
            $count += $this->processDuplicatesForLicenceVehicle($licenceVehicle, $licence);
        }

        $this->result->addMessage($count . ' vehicle(s) marked as duplicate');

        return $this->result;
    }

    protected function processDuplicatesForLicenceVehicle(
        Entity\Licence\LicenceVehicle $licenceVehicle,
        Entity\Licence\Licence $licence
    ) {
        $vrm = $licenceVehicle->getVehicle()->getVrm();

        $duplicates = $this->getRepo('LicenceVehicle')->fetchDuplicates($licence, $vrm);

        if ($duplicates === null) {
            return 0;
        }

        $count = 0;

        /** @var Entity\Licence\LicenceVehicle $duplicate */
        foreach ($duplicates as $duplicate) {
            $count++;
            $duplicate->markAsDuplicate();
            $this->getRepo('LicenceVehicle')->save($duplicate);
        }

        return $count;
    }
}
