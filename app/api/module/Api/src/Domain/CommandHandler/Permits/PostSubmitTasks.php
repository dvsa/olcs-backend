<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\StoreSnapshot as IrhpApplicationSnapshotCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Handles actions necessary once permit application is submitted.
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class PostSubmitTasks extends AbstractCommandHandler
{
    use QueueAwareTrait;

    protected $repoServiceName = 'IrhpApplication';

    /** @var IrhpCandidatePermitsCreator */
    private $irhpCandidatePermitsCreator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->irhpCandidatePermitsCreator = $mainServiceLocator->get(
            'PermitsCandidatePermitsIrhpCandidatePermitsCreator'
        );

        return parent::createService($serviceLocator);
    }

    /**
     * Handles post-submission tasks for IRHP Applications
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $id = $command->getId();

        $sideEffects = [
            IrhpApplicationSnapshotCmd::create(['id' => $id]),
        ];

        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->getRepo()->fetchById($id);
        $this->irhpCandidatePermitsCreator->createIfRequired($irhpApplication);

        $appSubmittedEmailCommand = $irhpApplication->getAppSubmittedEmailCommand();
        if ($appSubmittedEmailCommand) {
            $sideEffects[] = $this->emailQueue($appSubmittedEmailCommand, ['id' => $id], $id);
        }

        $this->result->merge(
            $this->handleSideEffects($sideEffects)
        );

        return $this->result;
    }
}
