<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Permits\AcceptScoring as AcceptScoringCmd;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList;
use Exception;

/**
 * Accept scoring
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AcceptScoring extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    const ISSUE_FEE_PRODUCT_REFERENCE = 'IRHP_GV_ECMT_100_PERMIT_FEE';

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitStock', 'FeeType', 'SystemParameter'];

    /**
     * Handle command
     *
     * @param AcceptScoringCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $stockId = $command->getId();

        $stockRepo = $this->getRepo('IrhpPermitStock');
        $stock = $stockRepo->fetchById($stockId);
        $stockRepo->refresh($stock);

        if ($stock->statusAllowsAcceptScoring()) {
            $prerequisiteResult = $this->handleQuery(
                CheckAcceptScoringPrerequisites::create(['id' => $stockId])
            );
        } else {
            $prerequisiteResult = [
                'result' => false,
                'message' => sprintf(
                    'Accept scoring is not permitted when stock status is \'%s\'',
                    $stock->getStatusDescription()
                )
            ];
        }

        if (!$prerequisiteResult['result']) {
            $stock->proceedToAcceptPrerequisiteFail($this->refData(IrhpPermitStock::STATUS_ACCEPT_PREREQUISITE_FAIL));
            $stockRepo->save($stock);

            $this->result->addMessage('Prerequisite failed: ' . $prerequisiteResult['message']);
            return $this->result;
        }

        $stock->proceedToAcceptInProgress($this->refData(IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS));
        $stockRepo->save($stock);

        try {
            $scoringResults = $this->handleQuery(
                GetScoredPermitList::create(['stockId' => $stockId])
            );

            $this->result->merge(
                $this->handleSideEffect(
                    UploadScoringResult::create([
                        'csvContent' => $scoringResults['result'],
                        'fileDescription' => 'Accepted Scoring Results'
                    ])
                )
            );

            $applicationIds = $this->getRepo()->fetchInScopeApplicationIds($stockId);

            $this->result->addMessage(
                sprintf('%d under consideration applications found', count($applicationIds))
            );

            foreach ($applicationIds as $applicationId) {
                $this->processApplication(
                    $this->getRepo()->fetchById($applicationId)
                );
            }

            $stock->proceedToAcceptSuccessful($this->refData(IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL));
            $this->result->addMessage('Acceptance process completed successfully.');
        } catch (Exception $e) {
            $stock->proceedToAcceptUnexpectedFail($this->refData(IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL));
            $this->result->addMessage('Acceptance process failed: ' . $e->getMessage());
        }

        $stockRepo->save($stock);
        return $this->result;
    }

    /**
     * Send outcome notification and create fees as required for an application
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     *
     * @throws Exception
     */
    private function processApplication(EcmtPermitApplication $ecmtPermitApplication)
    {
        $this->result->addMessage(
            sprintf('processing ecmt application with id %d:', $ecmtPermitApplication->getId())
        );

        $outcomeNotificationType = $ecmtPermitApplication->getOutcomeNotificationType();
        switch ($outcomeNotificationType) {
            case EcmtPermitApplication::NOTIFICATION_TYPE_EMAIL:
                $this->triggerEmailNotification($ecmtPermitApplication);
                break;
            case EcmtPermitApplication::NOTIFICATION_TYPE_MANUAL:
                $this->triggerManualNotification($ecmtPermitApplication);
                break;
            default:
                throw new Exception('Unknown notification type: ' . $outcomeNotificationType);
        }

        $this->createApplicationFeesAndUpdateStatus($ecmtPermitApplication);
        $this->getRepo()->save($ecmtPermitApplication);
    }

    /**
     * Send the outcome notification for an application requiring manual notification
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     */
    private function triggerManualNotification(EcmtPermitApplication $ecmtPermitApplication)
    {
        $this->result->merge(
            $this->handleSideEffect(
                $this->getCreateTaskCommand($ecmtPermitApplication)
            )
        );
    }

    /**
     * Send the outcome notification for an application requiring email notification
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     */
    private function triggerEmailNotification(EcmtPermitApplication $ecmtPermitApplication)
    {
        $successLevel = $ecmtPermitApplication->getSuccessLevel();

        if ($successLevel === EcmtPermitApplication::SUCCESS_LEVEL_NONE
            && $this->getRepo('SystemParameter')->fetchValue(SystemParameter::DISABLE_ECMT_ALLOC_EMAIL_NONE)
        ) {
            $this->result->addMessage(sprintf('- email sending disabled for %s', $successLevel));
            return;
        }

        $emailCommandLookup = [
            EcmtPermitApplication::SUCCESS_LEVEL_NONE => SendEcmtUnsuccessful::class,
            EcmtPermitApplication::SUCCESS_LEVEL_PARTIAL => SendEcmtPartSuccessful::class,
            EcmtPermitApplication::SUCCESS_LEVEL_FULL => SendEcmtSuccessful::class
        ];

        $emailCommand = $emailCommandLookup[$successLevel];

        $this->result->addMessage(
            sprintf('- sending email using command %s', $emailCommand)
        );

        $ecmtPermitApplicationId = $ecmtPermitApplication->getId();

        $this->result->merge(
            $this->handleSideEffect(
                $this->emailQueue(
                    $emailCommand,
                    [ 'id' => $ecmtPermitApplicationId ],
                    $ecmtPermitApplicationId
                )
            )
        );
    }

    /**
     * Create any applicable fees for an application and update the status accordingly
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     */
    private function createApplicationFeesAndUpdateStatus(EcmtPermitApplication $ecmtPermitApplication)
    {
        if ($ecmtPermitApplication->getSuccessLevel() == EcmtPermitApplication::SUCCESS_LEVEL_NONE) {
            $this->result->addMessage('- no fee applicable, set application to unsuccessful');

            $ecmtPermitApplication->proceedToUnsuccessful(
                $this->refData(EcmtPermitApplication::STATUS_UNSUCCESSFUL)
            );

            return;
        }

        $this->result->merge(
            $this->handleSideEffect(
                $this->getCreateIssueFeeCommand($ecmtPermitApplication)
            )
        );

        $this->result->addMessage('- create fee and set application to awaiting fee');

        $ecmtPermitApplication->proceedToAwaitingFee(
            $this->refData(EcmtPermitApplication::STATUS_AWAITING_FEE)
        );
    }

    /**
     * Get issue fee creation command for an application
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     *
     * @return CreateFee
     */
    private function getCreateIssueFeeCommand(EcmtPermitApplication $ecmtPermitApplication)
    {
        $feeType = $this->getRepo('FeeType')->getLatestByProductReference(self::ISSUE_FEE_PRODUCT_REFERENCE);
        $permitsAwarded = $ecmtPermitApplication->getPermitsAwarded();

        $feeDescription = sprintf(
            '%s - %d permits',
            $feeType->getDescription(),
            $permitsAwarded
        );

        return CreateFee::create(
            [
                'licence' => $ecmtPermitApplication->getLicence()->getId(),
                'ecmtPermitApplication' => $ecmtPermitApplication->getId(),
                'invoicedDate' => date('Y-m-d'),
                'description' => $feeDescription,
                'feeType' => $feeType->getId(),
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'amount' => $feeType->getFixedValue() * $permitsAwarded
            ]
        );
    }

    /**
     * Get task creation command for an application
     *
     * @param EcmtPermitApplication $application
     *
     * @return CreateTask
     */
    private function getCreateTaskCommand(EcmtPermitApplication $ecmtPermitApplication)
    {
        return CreateTask::create(
            [
                'category' => Task::CATEGORY_PERMITS,
                'subCategory' => Task::SUBCATEGORY_PERMITS_APPLICATION_OUTCOME,
                'description' => Task::TASK_DESCRIPTION_SEND_OUTCOME_LETTER,
                'ecmtPermitApplication' => $ecmtPermitApplication->getId(),
                'licence' => $ecmtPermitApplication->getLicence()->getId()
            ]
        );
    }
}
