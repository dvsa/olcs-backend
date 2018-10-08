<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Permits\ProcessEcmtApplications as ProcessEcmtApplicationsCmd;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use DateTime;

/**
 * Process ECMT applications
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class ProcessEcmtApplications extends AbstractCommandHandler implements TransactionedInterface
{
    use QueueAwareTrait;

    const APPLICATION_FEE_PRODUCT_REFERENCE = 'IRHP_GV_APP_ECMT';
    const ISSUE_FEE_PRODUCT_REFERENCE = 'IRHP_GV_ECMT_100_PERMIT_FEE';

    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitRange', 'IrhpCandidatePermit', 'FeeType'];

    /**
     * Handle command
     *
     * @param ProcessEcmtApplicationsCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $ecmtPermitApplications = $this->getRepo()->fetchUnderConsiderationApplications();
        $awaitingFeeStatus = $this->refData(EcmtPermitApplication::STATUS_AWAITING_FEE);
        $unsuccessfulStatus = $this->refData(EcmtPermitApplication::STATUS_UNSUCCESSFUL);

        $result = new Result();

        foreach ($ecmtPermitApplications as $ecmtPermitApplication) {
            $candidatePermitStatus = $this->applySuccessAndRangeAttributes($ecmtPermitApplication);

            if ($candidatePermitStatus['successful'] > 0) {
                $status = $awaitingFeeStatus;
            } else {
                $status = $unsuccessfulStatus;
            }
            $ecmtPermitApplication->setStatus($status);
            $this->getRepo()->save($ecmtPermitApplication);

            if ($candidatePermitStatus['successful'] > 0) {
                $result->merge(
                    $this->handleSideEffect(
                        $this->getIssueFeeCommand(
                            $ecmtPermitApplication,
                            $candidatePermitStatus['successful']
                        )
                    )
                );
            }

            $result->merge(
                $this->handleSideEffect(
                    $this->getEmailCommand(
                        $ecmtPermitApplication,
                        $candidatePermitStatus['requested'],
                        $candidatePermitStatus['successful']
                    )
                )
            );

        }

        $result->addMessage('Processing complete for ECMT application');

        return $result;
    }

    /**
     * Apply success and range attributes to a candidate permit
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     *
     * @return array
     */
    private function applySuccessAndRangeAttributes(EcmtPermitApplication $ecmtPermitApplication)
    {
        $irhpPermitApplications = $ecmtPermitApplication->getIrhpPermitApplications();
        $irhpPermitApplication = $irhpPermitApplications[0];
        $candidatePermits = $irhpPermitApplication->getIrhpCandidatePermits();

        $ecmtPermitApplicationId = $ecmtPermitApplication->getId();
        switch ($ecmtPermitApplicationId % 3) {
            case 1:
                $successfulCount = rand(1, count($candidatePermits)-1);
                break;
            case 2:
                $successfulCount = 0;
                break;
            default:
                $successfulCount = count($candidatePermits);
        }

        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
        $irhpPermitRanges = $this->getRepo('IrhpPermitRange')->getRanges(
            $irhpPermitStock->getId()
        );
        $lastRangeIndex = count($irhpPermitRanges) - 1;

        $candidateIndex = 1;
        foreach ($candidatePermits as $candidatePermit) {
            $successful = ($candidateIndex <= $successfulCount);
            if ($successful) {
                $range = $irhpPermitRanges[rand(0, $lastRangeIndex)];
            } else {
                $range = null;
            }
            $candidatePermit->setSuccessful($successful);
            $candidatePermit->setIrhpPermitRange($range);
            $this->getRepo('IrhpCandidatePermit')->save($candidatePermit);
            $candidateIndex++;
        }

        return [
            'requested' => count($candidatePermits),
            'successful' => $successfulCount
        ];
    }

    /**
     * Send outcome emails relating to an application
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     * @param int $requestedCandidatePermits
     * @param int $successfulCandidatePermits
     *
     * @return CommandInterface
     */
    private function getEmailCommand(
        EcmtPermitApplication $ecmtPermitApplication,
        $requestedCandidatePermits,
        $successfulCandidatePermits
    ) {
        if ($successfulCandidatePermits == 0) {
            $command = SendEcmtUnsuccessful::class;
        } elseif ($requestedCandidatePermits == $successfulCandidatePermits) {
            $command = SendEcmtSuccessful::class;
        } else {
            $command = SendEcmtPartSuccessful::class;
        }

        $ecmtPermitApplicationId = $ecmtPermitApplication->getId();
        return $this->emailQueue($command, ['id' => $ecmtPermitApplicationId], $ecmtPermitApplicationId);
    }

    /**
     * Get issue fee command for an application
     *
     * @param EcmtPermitApplication $ecmtPermitApplication
     * @param int $successfulCandidatePermits
     *
     * @return FeeEntity
     */
    private function getIssueFeeCommand(EcmtPermitApplication $ecmtPermitApplication, $successfulCandidatePermits)
    {
        $feeType = $this->getRepo('FeeType')->getLatestForEcmtPermit(self::ISSUE_FEE_PRODUCT_REFERENCE);

        $data = [
            'licence' => $ecmtPermitApplication->getLicence()->getId(),
            'ecmtPermitApplication' => $ecmtPermitApplication->getId(),
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeType->getDescription() . ' - ' . $successfulCandidatePermits . ' permits',
            'feeType' => $feeType->getId(),
            'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
            'amount' => $feeType->getFixedValue() * $successfulCandidatePermits
        ];

        return CreateFee::create($data);
    }
}
