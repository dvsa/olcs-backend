<?php

/**
 * Pay Outstanding Fees
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
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Pay Outstanding Fees
 * (initiates a CPMS payment which is a two-step process)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class PayOutstandingFees extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Payment';

    protected $extraRepos = ['Fee', 'FeePayment'];

    protected $cpmsHelper;

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        // get outstanding fees for organisation
        $outstandingFees = $this->getRepo('Fee')
            ->fetchOutstandingFeesByOrganisationId($command->getOrganisationId());

        // filter requested fee ids against outstanding fees
        $fees = $this->filterValid($command, $outstandingFees);

        // filter out fees that may have been paid by resolving outstanding payments
        $feesToPay = $this->resolvePaidFees($fees, $result);

        if (empty($feesToPay)) {
            $result->addMessage('No fees to pay');
            return $result;
        }

        // fire off to CPMS
        $response = $this->cpmsHelper->initiateCardRequest(
            $command->getOrganisationId(),
            $command->getCpmsRedirectUrl(),
            $feesToPay
        );

        // create payment
        $payment = new PaymentEntity();
        $payment->setGuid($response['receipt_reference']);
        $payment->setGatewayUrl($response['gateway_url']);
        $payment->setStatus($this->getRepo()->getRefdataReference(PaymentEntity::STATUS_OUTSTANDING));

        // create feePayment records
        $feePayments = new ArrayCollection();
        $payment->setFeePayments($feePayments);
        foreach ($feesToPay as $fee) {
            $feePayment = new FeePaymentEntity();
            $feePayment
                ->setFee($fee)
                ->setFeeValue($fee->getAmount())
                ->setPayment($payment); // needed for cascade persist to work
            $feePayments->add($feePayment);

            // update payment method on fee records
            $fee->setPaymentMethod($this->getRepo()->getRefdataReference($command->getPaymentMethod()));
        }

        // persist
        $this->getRepo()->save($payment);

        $result->addId('payment', $payment->getId());
        $result->addMessage('Payment record created');

        return $result;
    }

    protected function filterValid(CommandInterface $command, array $outstandingFees)
    {
        $fees = [];
        if (!empty($outstandingFees)) {
            $ids = $command->getFeeIds();
            foreach ($outstandingFees as $fee) {
                if (in_array($fee->getId(), $ids)) {
                    $fees[] = $fee;
                }
            }
        }
        return $fees;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->cpmsHelper = $serviceLocator->getServiceLocator()->get('CpmsHelperService');
        return $this;
    }

    /**
     * @param FeeEntity $fee
     * @param Result $result
     * @return boolean whether fee was paid
     */
    protected function resolveOutstandingPayments($fee, $result)
    {
        $paid = false;

        foreach ($fee->getFeePayments() as $fp) {
            if ($fp->getPayment()->isOutstanding()) {

                $paymentId = $fp->getPayment()->getId();

                // resolve outstanding payment
                $this->getCommandHandler()->handleCommand(
                    ResolvePaymentCommand::create(
                        [
                        'id' => $paymentId,
                        'paymentMethod' => $fee->getPaymentMethod(),
                        ]
                    )
                );

                // check payment status
                $payment = $this->getRepo()->fetchById($paymentId);
                $result->addMessage(
                    sprintf('payment %d resolved as %s', $paymentId, $payment->getStatus()->getDescription())
                );

                if ($payment->isPaid()) {
                    $paid = true;
                }
            }
        }

        return $paid;
    }

    /**
     * @param array $fees
     * @param Result $result
     * @return array
     */
    public function resolvePaidFees($fees, $result)
    {
        $feesToPay = [];
        foreach ($fees as $fee) {
            if ($fee->hasOutstandingPayment()) {
                $paid = $this->resolveOutstandingPayments($fee, $result);
                if (!$paid) {
                    $feesToPay[] = $fee;
                }
            } else {
                $feesToPay[] = $fee;
            }
        }
        return $feesToPay;
    }
}
