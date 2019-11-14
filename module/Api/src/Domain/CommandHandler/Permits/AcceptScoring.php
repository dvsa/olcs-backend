<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

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
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptConsts;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptScoringInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Service\Permits\Scoring\ScoringQueryProxy;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList;
use Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Accept scoring
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AcceptScoring extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitStock', 'FeeType', 'SystemParameter', 'IrhpApplication'];

    protected $applicationRepoName;

    /** @var ScoringQueryProxy */
    private $scoringQueryProxy;

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

        $this->scoringQueryProxy = $mainServiceLocator->get('PermitsScoringScoringQueryProxy');

        return parent::createService($serviceLocator);
    }

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

        $this->applicationRepoName = $stock->getApplicationRepoName();

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

            $applicationIds = $this->scoringQueryProxy->fetchInScopeUnderConsiderationApplicationIds($stockId);

            $this->result->addMessage(
                sprintf('%d under consideration applications found', count($applicationIds))
            );

            foreach ($applicationIds as $applicationId) {
                $this->processApplication(
                    $this->getRepo($this->applicationRepoName)->fetchById($applicationId)
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
     * @param ApplicationAcceptScoringInterface $application
     *
     * @throws Exception
     */
    private function processApplication(ApplicationAcceptScoringInterface $application)
    {
        $this->result->addMessage(
            sprintf('processing %s with id %d:', $application->getCamelCaseEntityName(), $application->getId())
        );

        $applicationAwardedPermits = $application->getSuccessLevel() != ApplicationAcceptConsts::SUCCESS_LEVEL_NONE;
        $applicationChecked = $application->getChecked();

        if ($applicationAwardedPermits && !$applicationChecked) {
            $this->result->addMessage('- application has been awarded permits and has not been checked, skipping');
            return;
        }

        $outcomeNotificationType = $application->getOutcomeNotificationType();
        switch ($outcomeNotificationType) {
            case ApplicationAcceptConsts::NOTIFICATION_TYPE_EMAIL:
                $this->triggerEmailNotification($application);
                break;
            case ApplicationAcceptConsts::NOTIFICATION_TYPE_MANUAL:
                $this->triggerManualNotification($application);
                break;
            default:
                throw new Exception('Unknown notification type: ' . $outcomeNotificationType);
        }

        $this->createApplicationFeesAndUpdateStatus($application);
        $this->getRepo($this->applicationRepoName)->save($application);
    }

    /**
     * Send the outcome notification for an application requiring manual notification
     *
     * @param ApplicationAcceptScoringInterface $application
     */
    private function triggerManualNotification(ApplicationAcceptScoringInterface $application)
    {
        $this->result->merge(
            $this->handleSideEffect(
                $this->getCreateTaskCommand($application)
            )
        );
    }

    /**
     * Send the outcome notification for an application requiring email notification
     *
     * @param ApplicationAcceptScoringInterface $application
     */
    private function triggerEmailNotification(ApplicationAcceptScoringInterface $application)
    {
        $successLevel = $application->getSuccessLevel();

        if ($successLevel === ApplicationAcceptConsts::SUCCESS_LEVEL_NONE
            && $this->getRepo('SystemParameter')->fetchValue(SystemParameter::DISABLE_ECMT_ALLOC_EMAIL_NONE)
        ) {
            $this->result->addMessage(sprintf('- email sending disabled for %s', $successLevel));
            return;
        }

        $emailCommandLookup = $application->getEmailCommandLookup();
        $emailCommand = $emailCommandLookup[$successLevel];

        $this->result->addMessage(
            sprintf('- sending email using command %s', $emailCommand)
        );

        $applicationId = $application->getId();

        $this->result->merge(
            $this->handleSideEffect(
                $this->emailQueue(
                    $emailCommand,
                    [ 'id' => $applicationId ],
                    $applicationId
                )
            )
        );
    }

    /**
     * Create any applicable fees for an application and update the status accordingly
     *
     * @param ApplicationAcceptScoringInterface $application
     */
    private function createApplicationFeesAndUpdateStatus(ApplicationAcceptScoringInterface $application)
    {
        if ($application->getSuccessLevel() == ApplicationAcceptConsts::SUCCESS_LEVEL_NONE) {
            $this->result->addMessage('- no fee applicable, set application to unsuccessful');

            $application->proceedToUnsuccessful(
                $this->refData(IrhpInterface::STATUS_UNSUCCESSFUL)
            );

            return;
        }

        $this->result->merge(
            $this->handleSideEffect(
                $this->getCreateIssueFeeCommand($application)
            )
        );

        $this->result->addMessage('- create fee and set application to awaiting fee');

        $application->proceedToAwaitingFee(
            $this->refData(IrhpInterface::STATUS_AWAITING_FEE)
        );
    }

    /**
     * Get issue fee creation command for an application
     *
     * @param ApplicationAcceptScoringInterface $application
     *
     * @return CreateFee
     */
    private function getCreateIssueFeeCommand(ApplicationAcceptScoringInterface $application)
    {
        $productReference = $application->getIssueFeeProductReference();

        $feeType = $this->getRepo('FeeType')->getLatestByProductReference($productReference);
        $permitsAwarded = $application->getPermitsAwarded();

        $feeDescription = sprintf(
            '%s - %d permits',
            $feeType->getDescription(),
            $permitsAwarded
        );

        return CreateFee::create(
            [
                'licence' => $application->getLicence()->getId(),
                $application->getCamelCaseEntityName() => $application->getId(),
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
     * @param ApplicationAcceptScoringInterface $application
     *
     * @return CreateTask
     */
    private function getCreateTaskCommand(ApplicationAcceptScoringInterface $application)
    {
        return CreateTask::create(
            [
                'category' => Task::CATEGORY_PERMITS,
                'subCategory' => Task::SUBCATEGORY_PERMITS_APPLICATION_OUTCOME,
                'description' => Task::TASK_DESCRIPTION_SEND_OUTCOME_LETTER,
                $application->getCamelCaseEntityName() => $application->getId(),
                'licence' => $application->getLicence()->getId()
            ]
        );
    }
}
