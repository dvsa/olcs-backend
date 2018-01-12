<?php

/**
 * Update ProposeToRevoke
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ProposeToRevoke;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Cases\ProposeToRevoke;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc;
use Dvsa\Olcs\Transfer\Command\Cases\ProposeToRevoke\UpdateProposeToRevokeSla as UpdateProposeToRevokeCommandSla;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\System\GenerateSlaTargetDate as GenerateSlaTargetDateCmd;

/**
 * Update ProposeToRevokeSla
 */
final class UpdateProposeToRevokeSla extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ProposeToRevoke';

    /**
     * Handle the command
     *
     * @param CommandInterface|UpdateProposeToRevokeCommandSla $command command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ProposeToRevoke $proposeToRevoke */
        $proposeToRevoke = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($command->getIsSubmissionRequiredForApproval() !== null) {
            $proposeToRevoke->setIsSubmissionRequiredForApproval($command->getIsSubmissionRequiredForApproval());
        }

        if ($command->getApprovalSubmissionIssuedDate() !== null) {
            $proposeToRevoke->setApprovalSubmissionIssuedDate($command->getApprovalSubmissionIssuedDate());
        }

        if ($command->getApprovalSubmissionReturnedDate() !== null) {
            $proposeToRevoke->setApprovalSubmissionReturnedDate($command->getApprovalSubmissionReturnedDate());
        }

        if ($command->getApprovalSubmissionPresidingTc() !== null) {
            $approvalSubmissionPresidingTc = $this->getRepo()
                ->getReference(PresidingTc::class, $command->getApprovalSubmissionPresidingTc());
            $proposeToRevoke->setApprovalSubmissionPresidingTc($approvalSubmissionPresidingTc);
        }

        if ($command->getIorLetterIssuedDate() !== null) {
            $proposeToRevoke->setIorLetterIssuedDate($command->getIorLetterIssuedDate());
        }

        if ($command->getOperatorResponseDueDate() !== null) {
            $proposeToRevoke->setOperatorResponseDueDate($command->getOperatorResponseDueDate());
        }

        if ($command->getOperatorResponseReceivedDate() !== null) {
            $proposeToRevoke->setOperatorResponseReceivedDate($command->getOperatorResponseReceivedDate());
        }

        if ($command->getIsSubmissionRequiredForAction() !== null) {
            $proposeToRevoke->setIsSubmissionRequiredForAction($command->getIsSubmissionRequiredForAction());
        }

        if ($command->getFinalSubmissionIssuedDate() !== null) {
            $proposeToRevoke->setFinalSubmissionIssuedDate($command->getFinalSubmissionIssuedDate());
        }

        if ($command->getFinalSubmissionReturnedDate() !== null) {
            $proposeToRevoke->setFinalSubmissionReturnedDate($command->getFinalSubmissionReturnedDate());
        }

        if ($command->getFinalSubmissionPresidingTc() !== null) {
            $finalSubmissionPresidingTc = $this->getRepo()
                ->getReference(PresidingTc::class, $command->getFinalSubmissionPresidingTc());
            $proposeToRevoke->setFinalSubmissionPresidingTc($finalSubmissionPresidingTc);
        }

        if ($command->getActionToBeTaken() !== null) {
            $proposeToRevoke->setActionToBeTaken($command->getActionToBeTaken());
        }

        if ($command->getRevocationLetterIssuedDate() !== null) {
            $proposeToRevoke->setRevocationLetterIssuedDate($command->getRevocationLetterIssuedDate());
        }

        if ($command->getNfaLetterIssuedDate() !== null) {
            $proposeToRevoke->setNfaLetterIssuedDate($command->getNfaLetterIssuedDate());
        }

        if ($command->getWarningLetterIssuedDate() !== null) {
            $proposeToRevoke->setWarningLetterIssuedDate($command->getWarningLetterIssuedDate());
        }

        if ($command->getPiAgreedDate() !== null) {
            $proposeToRevoke->setPiAgreedDate($command->getPiAgreedDate());
        }

        if ($command->getOtherActionAgreedDate() !== null) {
            $proposeToRevoke->setOtherActionAgreedDate($command->getOtherActionAgreedDate());
        }

        $this->getRepo()->save($proposeToRevoke);

        $result = new Result();
        $result->addId('proposeToRevoke', $proposeToRevoke->getId());
        $result->addMessage('Revocation Sla updated successfully');

        // generate all related SLA Target Dates
        $result->merge(
            $this->handleSideEffect(
                GenerateSlaTargetDateCmd::create(
                    [
                        'proposeToRevoke' => $proposeToRevoke->getId()
                    ]
                )
            )
        );

        return $result;
    }
}
