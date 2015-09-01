<?php

/**
 * Process Duplicate Vehicle Warning
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;

/**
 * Process Duplicate Vehicle Warning
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ProcessDuplicateVehicleWarning extends AbstractCommandHandler implements DocumentGeneratorAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'LicenceVehicle';

    public function handleCommand(CommandInterface $command)
    {
        $results = $this->getRepo()->fetchQueuedForWarning();

        // Nothing to process
        if (empty($results)) {
            return $this->result;
        }

        /** @var LicenceVehicle $licenceVehicle */
        foreach ($results as $licenceVehicle) {
            $vrm = $licenceVehicle->getVehicle()->getVrm();

            $countLicences = count($this->getRepo()->fetchLicencesForVrm($vrm));
        }
    }
}
