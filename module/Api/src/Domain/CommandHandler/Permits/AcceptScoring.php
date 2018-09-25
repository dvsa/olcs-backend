<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Permits\AcceptScoring as AcceptScoringCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Accept scoring
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class AcceptScoring extends AbstractStockCheckingCommandHandler
{
    use QueueAwareTrait;

    const ISSUE_FEE_PRODUCT_REFERENCE = 'IRHP_GV_ECMT_100_PERMIT_FEE';

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

        if (!$this->passesStockAvailabilityPrerequisite($stockId)) {
            $this->result->addMessage('Prerequisite failed: no permits remaining within this stock.');
            return $this->result;
        }

        $this->getRepo('IrhpPermitStock')->updateStatus($stockId, IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS);

        $applicationIds = $this->getRepo()->fetchUnderConsiderationApplicationIds($stockId);
        foreach ($applicationIds as $applicationId) {
            $this->processApplication(
                $this->getRepo()->fetchById($applicationId)
            );
        }

        $this->getRepo('IrhpPermitStock')->updateStatus($stockId, IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL);

        // TODO: write reports here?

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

        $permitsRequested = $ecmtPermitApplication->getPermitsRequired();
        $permitsAwarded = $irhpPermitApplication->countPermitsAwarded();

        if ($permitsAwarded == 0) {
            $emailCommand = SendEcmtUnsuccessful::class;
        } elseif ($permitsRequested == $permitsAwarded) {
            $emailCommand = SendEcmtSuccessful::class;
        } else {
            $emailCommand = SendEcmtPartSuccessful::class;
        }

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
        } else {
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
