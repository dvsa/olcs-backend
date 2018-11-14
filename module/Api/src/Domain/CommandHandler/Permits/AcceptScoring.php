<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Permits\AcceptScoring as AcceptScoringCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringLog;
use Exception;

/**
 * Accept scoring
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AcceptScoring extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait;

    use ToggleAwareTrait;

    const ISSUE_FEE_PRODUCT_REFERENCE = 'IRHP_GV_ECMT_100_PERMIT_FEE';

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitStock', 'IrhpPermitRange', 'IrhpPermit', 'FeeType'];

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

        // Get data for scoring results
        $dto = GetScoredPermitList::create(['stockId' => $stockId]);
        $scoringResults = $this->handleQuery($dto);

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
            $applicationIds = $this->getRepo()->fetchUnderConsiderationApplicationIds($stockId);

            $this->result->addMessage(
                sprintf('%d under consideration applications found', count($applicationIds))
            );

            foreach ($applicationIds as $applicationId) {
                $this->processApplication(
                    $this->getRepo()->fetchById($applicationId)
                );
            }

            $this->result->merge(
                $this->handleSideEffects([
                    UploadScoringResult::create([
                        'csvContent' => $scoringResults['result'],
                        'fileDescription' => 'Accepted Scoring Results'
                    ]),
                ])
            );

            $stock->proceedToAcceptSuccessful($this->refData(IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL));
            $stockRepo->save($stock);
        } catch (Exception $e) {
            $stock->proceedToAcceptUnexpectedFail($this->refData(IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL));
            $stockRepo->save($stock);

            return $this->result;
        }

        $this->result->addMessage('Acceptance of scoring completed successfully');
        return $this->result;
    }

    /**
     * Send email notification and create fees as required for an application
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     */
    private function processApplication(EcmtPermitApplication $ecmtPermitApplication)
    {
        $irhpPermitApplications = $ecmtPermitApplication->getIrhpPermitApplications();
        $irhpPermitApplication = $irhpPermitApplications[0];

        $this->result->addMessage(
            sprintf('processing ecmt application with id %d:', $ecmtPermitApplication->getId())
        );

        $permitsRequested = $ecmtPermitApplication->getPermitsRequired();
        $permitsAwarded = $irhpPermitApplication->countPermitsAwarded();

        $this->result->addMessage(
            sprintf('- permits requested: %d', $permitsRequested)
        );
        $this->result->addMessage(
            sprintf('- permits awarded: %d', $permitsAwarded)
        );

        $emailCommand = SendEcmtPartSuccessful::class;
        if ($permitsAwarded == 0) {
            $emailCommand = SendEcmtUnsuccessful::class;
        } elseif ($permitsRequested == $permitsAwarded) {
            $emailCommand = SendEcmtSuccessful::class;
        }

        $this->result->addMessage(
            sprintf('- sending email using command %s', $emailCommand)
        );

        $this->result->merge(
            $this->handleSideEffect(
                $this->emailQueue(
                    $emailCommand,
                    [ 'id' => $ecmtPermitApplication->getId() ],
                    $ecmtPermitApplication->getId()
                )
            )
        );

        if ($permitsAwarded > 0) {
            $this->result->merge(
                $this->handleSideEffect(
                    $this->getIssueFeeCommand($ecmtPermitApplication, $permitsAwarded)
                )
            );

            $ecmtPermitApplication->proceedToAwaitingFee(
                $this->refData(EcmtPermitApplication::STATUS_AWAITING_FEE)
            );

            $this->result->addMessage(
                sprintf('- creating fee and updating application status to %s', EcmtPermitApplication::STATUS_AWAITING_FEE)
            );
        } else {
            $this->result->addMessage(
                sprintf('- no fee applicable, updating application status to %s', EcmtPermitApplication::STATUS_UNSUCCESSFUL)
            );

            $ecmtPermitApplication->proceedToUnsuccessful(
                $this->refData(EcmtPermitApplication::STATUS_UNSUCCESSFUL)
            );
        }

        $this->getRepo()->save($ecmtPermitApplication);
    }

    /**
     * Get issue fee command for an application
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     * @param int $permitsAwarded
     *
     * @return Fee
     */
    private function getIssueFeeCommand(EcmtPermitApplication $ecmtPermitApplication, $permitsAwarded)
    {
        $feeType = $this->getRepo('FeeType')->getLatestForEcmtPermit(self::ISSUE_FEE_PRODUCT_REFERENCE);

        $data = [
            'licence' => $ecmtPermitApplication->getLicence()->getId(),
            'ecmtPermitApplication' => $ecmtPermitApplication->getId(),
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeType->getDescription() . ' - ' . $permitsAwarded . ' permits',
            'feeType' => $feeType->getId(),
            'feeStatus' => Fee::STATUS_OUTSTANDING,
            'amount' => $feeType->getFixedValue() * $permitsAwarded
        ];

        return CreateFee::create($data);
    }
}
