<?php

/**
 * Process Duplicate Vehicle Warnings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\Command\Vehicle\ProcessDuplicateVehicleWarning as ProcessDuplicateVehicleWarningCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;

/**
 * Process Duplicate Vehicle Warnings
 *
 * @NOTE We don't implement transactioned interface here, as we want to control what is transactioned
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ProcessDuplicateVehicleWarnings extends AbstractCommandHandler
{
    protected $repoServiceName = 'LicenceVehicle';

    public function handleCommand(CommandInterface $command)
    {
        $results = $this->getRepo()->fetchQueuedForWarning();

        // Nothing to process
        if (empty($results)) {
            return $this->result;
        }

        $count = 0;
        $countUnmarked = 0;
        $exceptions = [];

        /** @var LicenceVehicle $licenceVehicle */
        foreach ($results as $key => $licenceVehicle) {
            $vrm = $licenceVehicle->getVehicle()->getVrm();
            $licence = $licenceVehicle->getLicence();

            $duplicates = $this->getRepo()->fetchDuplicates($licence, $vrm, false);

            // No longer attached to more than 1 licence
            if (empty($duplicates)) {
                $licenceVehicle->removeDuplicateMark();
                $this->getRepo()->save($licenceVehicle);
                $countUnmarked++;
                continue;
            }

            $data = [
                'id' => $licenceVehicle->getId()
            ];

            try {
                $this->result->merge($this->handleSideEffect(ProcessDuplicateVehicleWarningCmd::create($data)));
                $count++;
            } catch (\Exception $ex) {
                // @todo handle exceptions
                $exceptions[] = $ex;
            }
        }

        $this->result->addMessage($count . ' letter(s) sent');
        $this->result->addMessage($countUnmarked . ' record(s) no longer duplicates');
        $this->result->addMessage(count($exceptions) . ' failed record(s)');

        return $this->result;
    }
}
