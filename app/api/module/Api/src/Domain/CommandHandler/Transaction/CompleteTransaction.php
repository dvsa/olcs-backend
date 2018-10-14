<?php

/**
 * Complete Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as SubmitApplicationCmd;
use Dvsa\Olcs\Transfer\Command\Permits\AcceptEcmtPermits;
use Dvsa\Olcs\Transfer\Command\Permits\CompleteIssuePayment;
use Dvsa\Olcs\Transfer\Command\Permits\EcmtSubmitApplication as SubmitEcmtPermitApplicationCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Complete Payment
 * (completes a CPMS payment)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CompleteTransaction extends AbstractCommandHandler implements TransactionedInterface, CpmsAwareInterface
{
    use CpmsAwareTrait;

    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Application', 'EcmtPermitApplication'];

    public function handleCommand(CommandInterface $command)
    {

        // ensure payment ref exists
        $reference = $command->getReference();

        // check payment status
        $transaction = $this->getRepo()->fetchbyReference($reference);
        if (!$transaction->isOutstanding()) {
            throw new ValidationException(
                ['Invalid transaction status: ' . $transaction->getStatus()->getId()]
            );
        }
        $fees = $transaction->getFees();

        foreach ($fees as $fee) {
            if (!empty($fee->getEcmtPermitApplication())) {
                $this->updateEcmtPermitApplication($fee);
            }
        }

        // update CPMS
        $this->getCpmsService()->handleResponse($reference, $command->getCpmsData(), reset($fees));

        // resolve payment
        $this->result->merge($this->resolvePayment($command, $transaction));

        // handle application submission
        if ($transaction->isPaid() && $command->getSubmitApplicationId()) {
            $this->result->merge($this->updateApplication($command));
        }

        $this->result->addId('transaction', $transaction->getId());
        $this->result->addMessage('CPMS record updated');
        return $this->result;
    }

    protected function resolvePayment($command, $transaction)
    {
        return $this->handleSideEffect(
            ResolvePaymentCommand::create(
                [
                    'id' => $transaction->getId(),
                    'paymentMethod' => $command->getPaymentMethod(),
                ]
            )
        );
    }

    /**
     * @param $command
     * @return Result
     */
    protected function updateApplication($command)
    {
        return $this->handleSideEffect(
            SubmitApplicationCmd::create(
                [
                    'id' => $command->getSubmitApplicationId(),
                    // we don't have the application version when we call
                    // this as an internal command - we would have to store
                    // it at the point we initiate the payment flow
                ]
            )
        );
    }

    /**
     * @param $command
     * @return Result
     */
    protected function updateEcmtPermitApplication($fee)
    {
        if ($fee->getEcmtPermitApplication()->canBeSubmitted()) {
            $this->result->merge($this->handleSideEffect(
                SubmitEcmtPermitApplicationCmd::create(['id' => $fee->getEcmtPermitApplication()->getId()])
            ));
        }
        if ($fee->getEcmtPermitApplication()->canBeAccepted()) {
            $this->result->merge($this->handleSideEffects([
                CompleteIssuePayment::create(['id' => $fee->getEcmtPermitApplication()->getId()]),
                AcceptEcmtPermits::create(['id' => $fee->getEcmtPermitApplication()->getId()])
            ]));
        }
    }
}
