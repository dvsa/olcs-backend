<?php

/**
 * RefuseApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as CreateSnapshotCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Licence\Refuse;

/**
 * Class RefuseApplication
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Application
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class RefuseApplication extends AbstractCommandHandler implements TransactionedInterface
{
    public $repoServiceName = 'Application';

    public $extraRepos = ['LicenceVehicle'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getId());

        $application->setStatus($this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_REFUSED));
        $application->setRefusedDate(new \DateTime());

        $this->getRepo()->save($application);

        $result->merge($this->createSnapshot($command->getId()));

        if ($application->getIsVariation() === false) {
            $result->merge(
                $this->handleSideEffect(
                    Refuse::create(
                        [
                            'id' => $application->getLicence()->getId()
                        ]
                    )
                )
            );
        }

        $result->merge(
            $this->handleSideEffect(
                CeaseGoodsDiscs::create(
                    [
                        'licenceVehicles' => $application->getLicence()->getLicenceVehicles()
                    ]
                )
            )
        );
        $this->clearLicenceVehicleSpecifiedDates($application->getLicence()->getLicenceVehicles());

        $result->addMessage('Application ' . $application->getId() . ' refused.');

        return $result;
    }

    protected function createSnapshot($applicationId)
    {
        $data = ['id' => $applicationId, 'event' => CreateSnapshotCmd::ON_REFUSE];
        return $this->handleSideEffect(CreateSnapshotCmd::create($data));
    }

    protected function clearLicenceVehicleSpecifiedDates($licenceVehilces)
    {
        foreach ($licenceVehilces as $licenceVehilce) {
            $licenceVehilce->setSpecifiedDate(null);
            $this->getRepo('LicenceVehicle')->save($licenceVehilce);
        }
    }
}
