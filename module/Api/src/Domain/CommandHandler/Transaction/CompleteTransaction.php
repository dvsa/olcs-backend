<?php

/**
 * Complete Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as SubmitApplicationCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Complete Payment
 * (completes a CPMS payment)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CompleteTransaction extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Application'];

    /**
     * @var \Dvsa\Olcs\Api\Service\CpmsHelperService $cpmsHelper
     */
    protected $cpmsHelper;

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

        // update CPMS
        $this->cpmsHelper->handleResponse($reference, $command->getCpmsData());

        // resolve payment
        $result->merge($this->resolvePayment($command, $transaction));

        // handle application submission
        if ($transaction->isPaid() && $command->getSubmitApplicationId()) {
            $result->merge($this->updateApplication($command));
        }

        $result->addId('transaction', $transaction->getId());
        $result->addMessage('CPMS record updated');
        return $result;
    }

    protected function resolvePayment($command, $transaction)
    {
        return  $this->getCommandHandler()->handleCommand(
            ResolvePaymentCommand::create(
                [
                    'id' => $transaction->getId(),
                    'paymentMethod' => $command->getPaymentMethod(),
                ]
            )
        );
    }

    protected function updateApplication($command)
    {
        return $this->getCommandHandler()->handleCommand(
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

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->cpmsHelper = $serviceLocator->getServiceLocator()->get('CpmsHelperService');
        return $this;
    }
}
