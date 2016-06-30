<?php

/**
 * WithdrawApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscsForApplication;
use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences;
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
        /* @var $application Application */
        $application = $this->getRepo()->fetchById($command->getId());

        $application->setStatus($this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_WITHDRAWN));
        $application->setWithdrawnDate(new \DateTime());
        $application->setWithdrawnReason($this->getRepo()->getRefdataReference($command->getReason()));

        $this->getRepo()->save($application);

        $this->result->merge($this->createSnapshot($command->getId()));

        if ($application->isNew()) {
            $this->result->merge(
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
            $this->result->merge($this->publishApplication($application));
            $this->result->merge($this->handleSideEffect(CloseTexTaskCmd::create(['id' => $application->getId()])));
        }

        // If Internal user close tasks
        if ($this->isInternalUser()) {
            $this->result->merge($this->handleSideEffect(CloseTexTaskCmd::create(['id' => $application->getId()])));
            $this->result->merge($this->handleSideEffect(CloseFeeDueTaskCmd::create(['id' => $application->getId()])));
        }

        $this->result->merge(
            $this->handleSideEffect(
                CeaseGoodsDiscsForApplication::create(['application' => $application->getId()])
            )
        );

        $this->getRepo('LicenceVehicle')->clearSpecifiedDateAndInterimApp($application);

        if ($application->getLicence()->getCommunityLics()->count() > 0) {

            $this->result->merge(
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
            $this->result->merge($this->handleSideEffect(EndInterimCmd::create(['id' => $application->getId()])));
        }

        $this->cancelS4($application);

        $this->cancelOutstandingFees($application);

        $this->result->addMessage('Application ' . $application->getId() . ' withdrawn.');

        return $this->result;
    }

    protected function createSnapshot($applicationId)
    {
        $data = ['id' => $applicationId, 'event' => CreateSnapshotCmd::ON_WITHDRAW];
        return $this->handleSideEffect(CreateSnapshotCmd::create($data));
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

    /**
     * Cancel any S4's attached to the application
     *
     * @param Application $application
     */
    protected function cancelS4(Application $application)
    {
        if ($application->isGoods()) {
            $this->result->merge(
                $this->handleSideEffect(
                    \Dvsa\Olcs\Transfer\Command\Application\Schedule41Cancel::create(['id' => $application->getId()])
                )
            );
        }
    }

    /**
     * Cancel outstanding fees on the application
     *
     * @param Application $application
     */
    private function cancelOutstandingFees(Application $application)
    {
        $this->result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Application\CancelOutstandingFees::create(
                    ['id' => $application->getId()]
                )
            )
        );
    }
}
