<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\StoreSnapshot as IrhpApplicationSnapshotCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Dvsa\Olcs\Api\Service\Permits\Scoring\CandidatePermitsCreator as ScoringCandidatePermitsCreator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Permits\StoreEcmtPermitApplicationSnapshot as SnapshotCmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Handles actions necessary once permit application is submitted.
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class PostSubmitTasks extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpApplication'];

    /** @var IrhpCandidatePermitsCreator */
    private $irhpCandidatePermitsCreator;

    /** @var ScoringCandidatePermitsCreator */
    private $scoringCandidatePermitsCreator;

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

        $this->scoringCandidatePermitsCreator = $mainServiceLocator->get('PermitsScoringCandidatePermitsCreator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handles post-submission tasks for ECMT Permit Applications
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        switch ($command->getIrhpPermitType()) {
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT:
                $this->handleEcmtPermitApplication($command);
                break;
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL:
                $this->handleIrhpApplication($command);
                break;
            default:
                throw new ValidationException(['Unsupported permit type, cannot proceed with post submission tasks.']);
        }

        return $this->result;
    }

    /**
     * Handles post-submission tasks for IRHP Applications
     *
     * @param CommandInterface $command
     *
     * @return void
     */
    private function handleIrhpApplication($command)
    {
        $id = $command->getId();

        $sideEffects = [
            IrhpApplicationSnapshotCmd::create(['id' => $id]),
        ];

        $irhpApplication = $this->getRepo('IrhpApplication')->fetchById($id);
        $this->irhpCandidatePermitsCreator->createIfRequired($irhpApplication);

        $appSubmittedEmailCommand = $irhpApplication->getAppSubmittedEmailCommand();
        if ($appSubmittedEmailCommand) {
            $sideEffects[] = $this->emailQueue($appSubmittedEmailCommand, ['id' => $id], $id);
        }

        $this->result->merge(
            $this->handleSideEffects($sideEffects)
        );
    }

    /**
     * Handles post-submission tasks for ECMT Permit Applications
     *
     * @param CommandInterface $command
     *
     * @return void
     */
    private function handleEcmtPermitApplication($command)
    {
        /**
         * @var EcmtPermitApplication       $application
         */
        $id = $command->getId();
        $application = $this->getRepo()->fetchById($id);

        // Create candidate permits for this application
        $this->scoringCandidatePermitsCreator->create(
            $application->getFirstIrhpPermitApplication(),
            $application->getRequiredEuro5(),
            $application->getRequiredEuro6()
        );

        // Setup necessary data to create HTML snapshot of Ecmt Permit Application
        $snapshotCmd = SnapshotCmd::create(['id' => $id]);

        // Prepare Submitted Email
        $emailCmd = $this->emailQueue(SendEcmtAppSubmittedCmd::class, ['id' => $id], $id);

        // Handle the side-effects configured above.
        $this->result->merge(
            $this->handleSideEffects([$emailCmd, $snapshotCmd])
        );
    }
}
