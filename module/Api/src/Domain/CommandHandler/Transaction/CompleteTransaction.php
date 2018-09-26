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

    protected $extraRepos = ['Application'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        // ensure payment ref exists
        $reference = $command->getReference();

        // check payment status
        $transaction = $this->getRepo()->fetchbyReference($reference);
        if (!$transaction->isOutstanding()) {
            throw new ValidationException(
                ['Invalid transaction status: '.$transaction->getStatus()->getId()]
            );
        }
        $fees = $transaction->getFees();

        // update CPMS
        $this->getCpmsService()->handleResponse($reference, $command->getCpmsData(), reset($fees));

        // resolve payment
        $result->merge($this->resolvePayment($command, $transaction));

        // handle application submission
        if ($transaction->isPaid() && $command->getSubmitApplicationId()) {
            $result->merge($this->updateApplication($command));
        } elseif ($transaction->isPaid() && $command->getSubmitEcmtPermitApplicationId()) {
            $result->merge($this->updateEcmtPermitApplication($command));
        }

        $result->addId('transaction', $transaction->getId());
        $result->addMessage('CPMS record updated');
        return $result;
    }

    protected function resolvePayment($command, $transaction)
    {
        return  $this->handleSideEffect(
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
    protected function updateEcmtPermitApplication($command)
    {
        return $this->handleSideEffect(
            SubmitEcmtPermitApplicationCmd::create(
                [
                    'id' => $command->getSubmitEcmtPermitApplicationId(),
                ]
            )
        );
    }
}
