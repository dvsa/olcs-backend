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
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
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

    protected $extraRepos = ['Fee'];

    protected $cpmsHelper;

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        if (!empty($command->getOrganisationId())) {
            // get outstanding fees for organisation
            $outstandingFees = $this->getRepo('Fee')
                ->fetchOutstandingFeesByOrganisationId($command->getOrganisationId());
            $customerReference = $command->getOrganisationId();
            // filter requested fee ids against outstanding fees
            $fees = $this->filterValid($command, $outstandingFees);
        } else {
            $fees = $this->getRepo('Fee')
                ->fetchOutstandingFeesByIds($command->getFeeIds());
            $customerReference = $this->getCustomerReference($fees);
        }

        // filter out fees that may have been paid by resolving outstanding payments
        $feesToPay = $this->resolvePaidFees($fees, $result);

        if (empty($feesToPay)) {
            $result->addMessage('No fees to pay');
            return $result;
        }

        switch ($command->getPaymentMethod()) {
            case FeeEntity::METHOD_CARD_ONLINE:
            case FeeEntity::METHOD_CARD_OFFLINE:
                return $this->cardPayment($customerReference, $command, $feesToPay, $result);
                break;
            case FeeEntity::METHOD_CASH:
                return $this->cashPayment($customerReference, $command, $feesToPay, $result);
                break;
            default:
                throw new ValidationException(['invalid payment method: ' . $command->getPaymentMethod()]);
                break;
        }

    }

    /**
     * @param string $customerReference
     * @param CommandInterface $command
     * @param array $feesToPay
     * @param Result $result
     *
     * @return Result
     */
    protected function cardPayment($customerReference, $command, $feesToPay, $result)
    {
        // fire off to CPMS
        $response = $this->cpmsHelper->initiateCardRequest(
            $customerReference,
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

    /**
     * @param string $customerReference
     * @param CommandInterface $command
     * @param array $fees
     * @param Result $result
     *
     * @return Result
     */
    protected function cashPayment($customerReference, $command, $fees, $result)
    {
        $this->checkAmountMatchesTotalDue($command->getReceived(), $fees);

        // fire off to CPMS to record payment
        $response = $this->cpmsHelper->recordCashPayment(
            $fees,
            $customerReference,
            $command->getReceived(),
            $command->getReceiptDate(),
            $command->getPayer(),
            $command->getSlipNo()
        );

        // update fee records as paid
        foreach ($fees as $fee) {
            $fee
                ->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID))
                // @TODO sort out date handling
                ->setReceivedDate($this->cpmsHelper->getDateObjectFromArray($command->getReceiptDate()))
                ->setReceiptNo($response['receipt_reference'])
                ->setPaymentMethod($this->getRepo()->getRefdataReference(FeeEntity::METHOD_CASH))
                ->setPayerName($command->getPayer())
                ->setPayingInSlipNumber($command->getSlipNo())
                ->setReceivedAmount($fee->getAmount());

            $this->getRepo('Fee')->save($fee);
            // @todo trigger listener
        }

        $result->addMessage('Fee(s) updated as Paid');

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
                $dto = ResolvePaymentCommand::create(
                    [
                    'id' => $paymentId,
                    'paymentMethod' => $fee->getPaymentMethod(),
                    ]
                );
                $this->getCommandHandler()->handleCommand($dto);

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

    /**
     * Gets Customer Reference based on the fees details
     * The method assumes that all fees link to the same organisationId
     *
     * @param array $fees
     * @return int organisationId
     */
    protected function getCustomerReference($fees)
    {
        if (empty($fees)) {
            return null;
        }

        $reference = 'Miscellaneous'; // default value

        foreach ($fees as $fee) {
            if (!empty($fee->getLicence())) {
                $organisation = $fee->getLicence()->getOrganisation();
                if (!empty($organisation)) {
                    $reference = $organisation->getId();
                    break;
                }
            }
        }

        return $reference;
    }

    /**
     * @param array $fees
     * return float
     */
    protected function getTotalAmountFromFees($fees)
    {
        $totalAmount = 0;
        foreach ($fees as $fee) {
            $totalAmount += (float)$fee->getAmount();
        }
        return $totalAmount;
    }

    /**
     * Partial payments are not supported for cash/cheque/PO payments.
     * The form validation will normally catch any mismatch but it relies on a
     * hidden field so we have a secondary check here in the service layer.
     *
     * @param string $amount
     * @param array $fees
     * @return null
     * @throws ValidationException
     *
     * @note We compare the formatted amounts as comparing floats for equality
     * doesn't work!!
     * @see http://php.net/manual/en/language.types.float.php
     */
    protected function checkAmountMatchesTotalDue($amount, $fees)
    {
        $amount    = $this->cpmsHelper->formatAmount($amount);
        $totalFees = $this->cpmsHelper->formatAmount($this->getTotalAmountFromFees($fees));
        if ($amount !== $totalFees) {
            throw new ValidationException(["Amount must match the fee(s) due"]);
        }
    }
}
