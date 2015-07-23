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
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
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

    /**
     * @var \Dvsa\Olcs\Api\Service\CpmsHelperService $cpmsHelper
     */
    protected $cpmsHelper;

    public function handleCommand(CommandInterface $command)
    {
        /* @var $payment Payment */
        $payment = $this->getRepo()->fetchUsingId($command);

        $cpmsStatus = $this->cpmsHelper->getPaymentStatus($payment->getGuid());

        $now = new \DateTime();

        $result = new Result();

        switch ($cpmsStatus) {
            case Cpms::PAYMENT_SUCCESS:
                $status = Payment::STATUS_PAID;
                $payment->setCompletedDate($now);
                $paymentMethodRef = $this->getRepo()->getRefdataReference($command->getPaymentMethod());
                $feeStatusRef = $this->getRepo()->getRefdataReference(Fee::STATUS_PAID);
                foreach ($payment->getFeePayments() as $fp) {
                    $fee = $fp->getFee();
                    $fee
                        ->setFeeStatus($feeStatusRef)
                        ->setReceivedDate($now)
                        ->setReceiptNo($payment->getGuid())
                        ->setPaymentMethod($paymentMethodRef)
                        ->setReceivedAmount($fee->getAmount());
                    $this->getRepo('Fee')->save($fee);
                    // trigger side effects
                    $result->merge(
                        $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
                    );
                }
                break;
            case Cpms::PAYMENT_FAILURE:
                $status = Payment::STATUS_FAILED;
                break;
            case Cpms::PAYMENT_CANCELLATION:
                $status = Payment::STATUS_CANCELLED;
                break;
            case Cpms::PAYMENT_IN_PROGRESS:
                // resolve any abandoned payments as 'failed'
                $status = Payment::STATUS_FAILED;
                break;
            default:
                throw new ValidationException(['Unknown CPMS payment_status: '.$cpmsStatus]);
        }

        $payment->setStatus($this->getRepo()->getRefdataReference($status));
        $this->getRepo()->save($payment);

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
