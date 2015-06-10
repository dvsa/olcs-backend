<?php

/**
 * Pay Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeePayment as FeePaymentEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
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
        $outstandingFees = $this->feeRepo->fetchOutstandingFeesByOrganisationId($command->getOrganisationId());

        // filter requested fee ids against outstanding fees
        $fees = $this->filterValid($command, $outstandingFees);

        // filter out fees that may have been paid by resolving outstanding payments
        $feesToPay = $this->cpmsHelper->resolvePaidFees($fees);

        if (empty($feesToPay)) {
            $result->addMessage('No fees to pay');
            return $result;
        }

        // fire off to CPMS
        $response = $this->cpmsHelper->initiateCardRequest(
            $command->getOrganisationId(),
            $command->getCpmsRedirectUrl(),
            $feesToPay,
            $command->getPaymentMethod()
        );

        // record payment
        $payment = new PaymentEntity();
        $payment->setGuid($response['receipt_reference']);
        $payment->setGatewayUrl($response['gateway_url']);
        $payment->setStatus($this->getRepo()->getRefdataReference(PaymentEntity::STATUS_OUTSTANDING));
        $this->getRepo()->save($payment);
        $result->addId('payment', $payment->getId());

        // record feePayments and fee payment method
        foreach ($feesToPay as $fee) {
            $feePayment = new FeePaymentEntity();
            $feePayment->setPayment($payment);
            $feePayment->setFee($fee);
            $feePayment->setFeeValue($fee->getAmount());
            $this->getRepo('FeePayment')->save($feePayment);

            // ensure payment method is recorded
            $fee->setPaymentMethod($this->getRepo()->getRefdataReference($command->getPaymentMethod()));
            $this->getRepo('Fee')->save($fee);
        }

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
}
