<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\Command\Vehicle\RemoveDuplicateVehicle as RemoveDuplicateVehicleCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;

/**
 * Process Duplicate Vehicle Removal
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class ProcessDuplicateVehicleRemoval extends AbstractCommandHandler
{
    protected $repoServiceName = 'LicenceVehicle';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $results = $this->getRepo()->fetchForRemoval();

        if (empty($results)) {
            $this->result->addMessage('Nothing to process');
            return $this->result;
        }

        $count = 0;
        $countRemoved = 0;
        $countFailed = 0;
        $removedVehicles = [];

        /** @var LicenceVehicle $licenceVehicle */
        foreach ($results as $key => $licenceVehicle) {
            $vrm = $licenceVehicle->getVehicle()->getVrm();
            $licence = $licenceVehicle->getLicence();

            $duplicates = $this->getRepo()->fetchDuplicates($licence, $vrm, false);

            // No longer attached to more than 1 licence
            if (empty($duplicates)) {
                $licenceVehicle->removeDuplicateMark(true);
                $this->getRepo()->save($licenceVehicle);
                $countRemoved++;
                continue;
            }

            $data = [
                'id' => $licenceVehicle->getId()
            ];
            try {
                $this->result->merge($this->handleSideEffect(RemoveDuplicateVehicleCmd::create($data)));
                $this->result->addMessage($licenceVehicle->getId() . ' succeeded');
                $count++;
                $removedVehicles[] = [
                    'vrm' => $licenceVehicle->getVehicle()->getVrm(),
                    'licNo' => $licenceVehicle->getLicence()->getLicNo()
                ];
            } catch (\Exception $ex) {
                $this->result->addMessage($licenceVehicle->getId() . ' failed: ' . $ex->getMessage());
                $countFailed++;
            }
        }
        if (count($removedVehicles) > 0) {
            $this->sendReport($removedVehicles);
        }

        $this->result->addMessage($count . ' vehicle(s) removed');
        $this->result->addMessage($countRemoved . ' record(s) no longer duplicates');
        $this->result->addMessage($countFailed . ' failed record(s)');

        return $this->result;
    }

    /**
     * Send report
     *
     * @param array $removedVehicles removed vehicles
     *
     * @return void
     */
    protected function sendReport($removedVehicles)
    {
        $emailAddress = $this->getRepo('SystemParameter')->fetchValue(SystemParameter::DUPLICATE_VEHICLE_EMAIL_LIST);
        if (empty($emailAddress)) {
            return;
        }
        usort(
            $removedVehicles, function ($a, $b) {
            if ($a['qualificationType']['displayOrder'] == $b['qualificationType']['displayOrder']) {
                return 0;
            }
            return ($a['qualificationType']['displayOrder'] > $b['qualificationType']['displayOrder']) ? +1 : -1;
        }
        );

    }
}
