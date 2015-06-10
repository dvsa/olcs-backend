<?php

/**
 * Complete Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Payment\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeePayment as FeePaymentEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Complete Payment
 * (initiates completes a CPMS payment)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CompletePayment extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Payment';

    protected $cpmsHelper;

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        // ensure payment ref exists
        $reference = $command->getReference();

        // check payment status
        $payment = $this->getRepo()->fetchbyReference($reference);
        if ($payment->getStatus()->getId() !== PaymentEntity::STATUS_OUTSTANDING) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(
                ['Invalid payment status: '.$payment->getStatus()->getId()]
            );
        }

        // update CPMS
        $this->cpmsHelper->handleResponse($reference, $command->getCpmsData());

        // resolve payment
        $result->merge($this->resolvePayment($command, $payment));

        $result->addId('payment', $payment->getId());
        $result->addMessage('CPMS record updated');
        return $result;
    }

    protected function resolvePayment($command, $payment)
    {
        return  $this->getCommandHandler()->handleCommand(
            ResolvePaymentCommand::create([
                'id' => $payment->getId(),
                'paymentMethod' => $command->getPaymentMethod(),
            ])
        );
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->cpmsHelper = $serviceLocator->getServiceLocator()->get('CpmsHelperService');
        return $this;
    }
}
