<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Dvsa\Olcs\Api\Domain\Command\Permits\StoreEcmtPermitApplicationSnapshot as SnapshotCmd;

/**
 * Handles actions necessary once EcmtPermitApplication is marked as submitted.
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class EcmtPostSubmitTasks extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpCandidatePermit'];

    /**
     * Handles post-submission tasks for ECMT Permit Applications
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplication       $application
         */
        $id = $command->getId();
        $application = $this->getRepo()->fetchById($id);

        // Create candidate permits for this application
        $this->createIrhpCandidatePermitRecords(
            $application->getPermitsRequired(),
            $application->getFirstIrhpPermitApplication()
        );

        // Setup necessary data to create HTML snapshot of Ecmt Permit Application
        $snapshotCmd = SnapshotCmd::create(['id' => $id]);

        // Prepare Submitted Email
        $emailCmd = $this->emailQueue(SendEcmtAppSubmittedCmd::class, ['id' => $id], $id);

        // Handle the side-effects configured above.
        $this->result->merge(
            $this->handleSideEffects([$emailCmd, $snapshotCmd])
        );

        return $this->result;
    }

    /**
     * @param int $permitsRequired
     * @param IrhpPermitApplicationEntity $irhpPermitApplication
     */
    private function createIrhpCandidatePermitRecords(int $permitsRequired, IrhpPermitApplicationEntity $irhpPermitApplication)
    {
        $intensityOfUse = floatval($irhpPermitApplication->getPermitIntensityOfUse());
        $applicationScore = floatval($irhpPermitApplication->getPermitApplicationScore());

        for ($i = 0; $i < $permitsRequired; $i++) {
            $candidatePermit = IrhpCandidatePermitEntity::createNew(
                $irhpPermitApplication,
                $intensityOfUse,
                $applicationScore
            );
            $this->getRepo('IrhpCandidatePermit')->save($candidatePermit);
        }
    }
}
