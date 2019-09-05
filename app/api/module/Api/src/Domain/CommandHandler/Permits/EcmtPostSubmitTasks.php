<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
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

    protected $extraRepos = ['IrhpCandidatePermit', 'SystemParameter'];

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

        return $this->result;
    }

    /**
     * Create IRHP Candidate Permit records for each emissions category
     *
     * @param IrhpPermitApplicationEntity $irhpPermitApplication
     * @param int $requiredEuro5
     * @param int $requiredEuro6
     *
     * @return void
     */
    private function createIrhpCandidatePermitRecords(
        IrhpPermitApplicationEntity $irhpPermitApplication,
        $requiredEuro5 = 0,
        $requiredEuro6 = 0
    ) {
        if ($requiredEuro5 > 0) {
            $this->createIrhpCandidatePermitRecordsForEmissionsCategory(
                $irhpPermitApplication,
                RefData::EMISSIONS_CATEGORY_EURO5_REF,
                $requiredEuro5
            );
        }

        if ($requiredEuro6 > 0) {
            $this->createIrhpCandidatePermitRecordsForEmissionsCategory(
                $irhpPermitApplication,
                RefData::EMISSIONS_CATEGORY_EURO6_REF,
                $requiredEuro6
            );
        }
    }

    /**
     * Create IRHP Candidate Permit records for a given emissions category
     *
     * @param IrhpPermitApplicationEntity $irhpPermitApplication
     * @param string $emissionsCategory
     * @param int $permitsRequired
     *
     * @return void
     */
    private function createIrhpCandidatePermitRecordsForEmissionsCategory(
        IrhpPermitApplicationEntity $irhpPermitApplication,
        string $emissionsCategory,
        int $permitsRequired
    ) {
        $useAltEcmtIouAlgorithm = $this->getRepo('SystemParameter')->fetchValue(
            SystemParameter::USE_ALT_ECMT_IOU_ALGORITHM
        );

        $scoringEmissionsCategory = null;
        if ($useAltEcmtIouAlgorithm) {
            $scoringEmissionsCategory = $emissionsCategory;
        }

        $intensityOfUse = floatval($irhpPermitApplication->getPermitIntensityOfUse($scoringEmissionsCategory));
        $applicationScore = floatval($irhpPermitApplication->getPermitApplicationScore($scoringEmissionsCategory));

        for ($i = 0; $i < $permitsRequired; $i++) {
            $candidatePermit = IrhpCandidatePermitEntity::createNew(
                $irhpPermitApplication,
                $this->refData($emissionsCategory),
                $intensityOfUse,
                $applicationScore
            );
            $this->getRepo('IrhpCandidatePermit')->save($candidatePermit);
        }
    }
}
