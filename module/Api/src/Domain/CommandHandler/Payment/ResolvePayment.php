<?php

/**
 * Resolve Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Fee\Payment;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\CpmsHelperService as Cpms;

/**
 * Resolve Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class ResolvePayment extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Payment';

    protected $extraRepos = ['Fee'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $payment Payment */
        $payment = $this->getRepo()->fetchUsingId($command);

        $cpmsStatus  = $this->cpmsHelper->getPaymentStatus($payment->getGuid());

        $now = new \DateTime();

        switch ($cpmsStatus) {
            case Cpms::PAYMENT_SUCCESS:
                $status = Payment::STATUS_PAID;
                $payment->setCompletedDate($now);
//
                foreach ($payment->getFeePayments() as $fp) {
                    foreach ($fp->getFee() as $fee) {
                        $fee
                            ->setFeeStatus($this->getRepo()->getRefdataReference(Fee::STATUS_PAID))
                            ->setReceivedDate($now)
                            ->setReceiptNo($payment->getGuid())
                            ->setPaymentMethod($command->getPaymentMethod())
                            ->receivedAmount($fee->getAmount());
                        $this->getRepo('Fee')->save($fee);
                    }
                }
//
                break;
            case Cpms::PAYMENT_FAILURE:
                $status = PaymentEntityService::STATUS_FAILED;
                break;
            case Cpms::PAYMENT_CANCELLATION:
                $status = PaymentEntityService::STATUS_CANCELLED;
                break;
            case Cpms::PAYMENT_IN_PROGRESS:
                // resolve any abandoned payments as 'failed'
                $status = PaymentEntityService::STATUS_FAILED;
                break;
            default:
                throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(
                    ['Unknown CPMS payment_status: ' . $cpmsStatus]
                );
        }

        $payment->setStatus($this->getRepo()->getRefdataReference(Payment::STATUS_PAID));
        $this->getRepo()->save($payment);

        $result = new Result();
        $result->addId('payment', $payment->getId());
        $result->addMessage('Payment resolved as '. $payment->getStatus()->getDescription());

        return $result;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->cpmsHelper = $serviceLocator->getServiceLocator()->get('CpmsHelperService');
        return $this;
    }
}
