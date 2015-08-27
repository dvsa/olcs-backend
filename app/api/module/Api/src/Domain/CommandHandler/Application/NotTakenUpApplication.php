<?php

/**
 * NotTakenUpApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as CreateSnapshotCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Licence\NotTakenUp;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Delete;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\Void;

/**
 * Class NotTakenUpApplication
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Application
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class NotTakenUpApplication extends AbstractCommandHandler implements TransactionedInterface
{
    public $repoServiceName = 'Application';

    public $extraRepos = ['LicenceVehicle'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getId());

        $application->setStatus($this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_NOT_TAKEN_UP));
        $application->setWithdrawnDate(new \DateTime());

        $this->getRepo()->save($application);

        $result->merge($this->createSnapshot($command->getId()));

        $result->merge(
            $this->handleSideEffect(
                NotTakenUp::create(
                    [
                        'id' => $application->getLicence()->getId()
                    ]
                )
            )
        );

        $result->merge(
            $this->handleSideEffect(
                CeaseGoodsDiscs::create(
                    [
                        'licenceVehicles' => $application->getLicence()->getLicenceVehicles()
                    ]
                )
            )
        );
        $this->clearLicenceVehicleSpecifiedDatesAndInterimApp($application->getLicence()->getLicenceVehicles());

        $result->merge(
            $this->handleSideEffect(
                RemoveLicenceVehicle::create(
                    [
                        'licenceVehicles' => $application->getLicence()->getLicenceVehicles()
                    ]
                )
            )
        );

        $transportManagers = $application->getTransportManagers()->toArray();
        if (!empty($transportManagers)) {
            $result->merge(
                $this->handleSideEffect(
                    Delete::create(
                        [
                            'ids' => array_map(
                                function ($tm) {
                                    return $tm->getId();
                                },
                                $transportManagers
                            )
                        ]
                    )
                )
            );
        }

        $communityLicences = $application->getLicence()->getCommunityLics()->toArray();
        if (!empty($communityLicences)) {
            $result->merge(
                $this->handleSideEffect(
                    Void::create(
                        [
                            'licence' => $application->getLicence(),
                            'communityLicences' => array_map(
                                function ($cl) {
                                    return $cl->getId();
                                },
                                $communityLicences
                            )
                        ]
                    )
                )
            );
        }

        $result->addMessage('Application ' . $application->getId() . ' set to not taken up.');

        return $result;
    }

    protected function createSnapshot($applicationId)
    {
        $data = ['id' => $applicationId, 'event' => CreateSnapshotCmd::ON_NTU];
        return $this->handleSideEffect(CreateSnapshotCmd::create($data));
    }

    protected function clearLicenceVehicleSpecifiedDatesAndInterimApp($licenceVehilces)
    {
        foreach ($licenceVehilces as $licenceVehilce) {
            $licenceVehilce->setSpecifiedDate(null);
            $licenceVehilce->setInterimApplication(null);
            $this->getRepo('LicenceVehicle')->save($licenceVehilce);
        }
    }
}
