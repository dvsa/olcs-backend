<?php

/**
 * Complete Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Payment\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Fee\FeePayment as FeePaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as SubmitApplicationCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Complete Payment
 * (completes a CPMS payment)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CompletePayment extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Payment';

    protected $extraRepos = ['Application'];

    protected $cpmsHelper;

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        // ensure payment ref exists
        $reference = $command->getReference();

        // check payment status
        $payment = $this->getRepo()->fetchbyReference($reference);
        if (!$payment->isOutstanding()) {
            throw new ValidationException(
                ['Invalid payment status: '.$payment->getStatus()->getId()]
            );
        }

        // update CPMS
        $this->cpmsHelper->handleResponse($reference, $command->getCpmsData());

        // resolve payment
        $result->merge($this->resolvePayment($command, $payment));

        // handle application submission
        if ($payment->isPaid() && $command->getSubmitApplicationId()) {
            $result->merge($this->updateApplication($command));
        }

        $result->addId('payment', $payment->getId());
        $result->addMessage('CPMS record updated');
        return $result;
    }

    protected function resolvePayment($command, $payment)
    {
        return  $this->getCommandHandler()->handleCommand(
            ResolvePaymentCommand::create(
                [
                    'id' => $payment->getId(),
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
