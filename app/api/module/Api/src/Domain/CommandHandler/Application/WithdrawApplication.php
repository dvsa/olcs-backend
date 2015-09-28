<?php

/**
 * WithdrawApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as CreateSnapshotCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Licence\Withdraw;
use Dvsa\Olcs\Api\Domain\Command\Application\EndInterim as EndInterimCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask as CloseTexTaskCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\CloseFeeDueTask as CloseFeeDueTaskCmd;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Class WithdrawApplication
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Application
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class WithdrawApplication extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    public $repoServiceName = 'Application';

    public $extraRepos = ['LicenceVehicle'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $application Application */
        $application = $this->getRepo()->fetchById($command->getId());

        $application->setStatus($this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_WITHDRAWN));
        $application->setWithdrawnDate(new \DateTime());
        $application->setWithdrawnReason($this->getRepo()->getRefdataReference($command->getReason()));

        $this->getRepo()->save($application);

        $result->merge($this->createSnapshot($command->getId()));

        if ($application->isNew()) {
            $result->merge(
                $this->handleSideEffect(
                    Withdraw::create(
                        [
                            'id' => $application->getLicence()->getId()
                        ]
                    )
                )
            );
        }

        if ($application->isPublishable()) {
            $result->merge($this->publishApplication($application));
            $result->merge($this->handleSideEffect(CloseTexTaskCmd::create(['id' => $application->getId()])));
        }

        // If Internal user close tasks
        if ($this->isInternalUser()) {
            $result->merge($this->handleSideEffect(CloseTexTaskCmd::create(['id' => $application->getId()])));
            $result->merge($this->handleSideEffect(CloseFeeDueTaskCmd::create(['id' => $application->getId()])));
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
        $this->clearLicenceVehicleSpecifiedDatesAndInterimApp($application->getLicence()->getLicenceVehicles());

        $communityLicences = $application->getLicence()->getCommunityLics()->toArray();
        if (!empty($communityLicences)) {
            $result->merge(
                $this->handleSideEffect(
                    ReturnAllCommunityLicences::create(
                        [
                            'id' => $application->getLicence()->getId(),
                        ]
                    )
                )
            );
        }

        if (
            $application->isGoods() &&
            $application->getCurrentInterimStatus() === Application::INTERIM_STATUS_INFORCE
            ) {
            $result->merge($this->handleSideEffect(EndInterimCmd::create(['id' => $application->getId()])));
        }

        $result->addMessage('Application ' . $application->getId() . ' withdrawn.');

        return $result;
    }

    protected function createSnapshot($applicationId)
    {
        $data = ['id' => $applicationId, 'event' => CreateSnapshotCmd::ON_WITHDRAW];
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

    /**
     * Publish the application
     *
     * @param Application $application
     *
     * @return Result
     */
    protected function publishApplication(Application $application)
    {
        return $this->handleSideEffect(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::create(
                [
                    'id' => $application->getId(),
                    'trafficArea' => $application->getTrafficArea()->getId(),
                ]
            )
        );
    }
}
