<?php

/**
 * Pay Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Payment\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeePayment as FeePaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\Application\OutstandingFees as AppOutstandingFeesQuery;
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

    protected $extraRepos = ['Fee', 'FeeType', 'Application'];

    protected $cpmsHelper;

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        // @TODO refactor this - split up commands or add validation to Transfer query
        // The three valid use cases are:
        // organisationId AND feeIds
        // applicationId only
        // feeIds only
        if (!empty($command->getOrganisationId())) {
            // get outstanding fees for organisation
            $outstandingFees = $this->getRepo('Fee')
                ->fetchOutstandingFeesByOrganisationId($command->getOrganisationId());
            $customerReference = $command->getOrganisationId();
            // filter requested fee ids against outstanding fees
            $fees = $this->filterValid($command, $outstandingFees);
        } elseif (!empty($command->getApplicationId())) {
            $fees = $this->getOutstandingFeesForApplication($command->getApplicationId());
            $customerReference = $this->getCustomerReference($fees);
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
            case FeeEntity::METHOD_CASH:
                return $this->cashPayment($customerReference, $command, $feesToPay, $result);
            case FeeEntity::METHOD_CHEQUE:
                return $this->chequePayment($customerReference, $command, $feesToPay, $result);
            case FeeEntity::METHOD_POSTAL_ORDER:
                return $this->poPayment($customerReference, $command, $feesToPay, $result);
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

        $receiptDate = new \DateTime($command->getReceiptDate());
        $feeStatusRef = $this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID);
        $paymentMethodRef = $this->getRepo()->getRefdataReference(FeeEntity::METHOD_CASH);

        // update fee records as paid
        foreach ($fees as $fee) {
            $fee
                ->setFeeStatus($feeStatusRef)
                ->setReceivedDate($receiptDate)
                ->setReceiptNo($response['receipt_reference'])
                ->setPaymentMethod($paymentMethodRef)
                ->setPayerName($command->getPayer())
                ->setPayingInSlipNumber($command->getSlipNo())
                ->setReceivedAmount($fee->getAmount());

            $this->getRepo('Fee')->save($fee);

            // trigger side effects
            $result->merge(
                $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
            );
        }

        $result->addMessage('Fee(s) updated as Paid by cash');

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
    protected function chequePayment($customerReference, $command, $fees, $result)
    {
        $this->checkAmountMatchesTotalDue($command->getReceived(), $fees);

        // fire off to CPMS to record payment
        $response = $this->cpmsHelper->recordChequePayment(
            $fees,
            $customerReference,
            $command->getReceived(),
            $command->getReceiptDate(),
            $command->getPayer(),
            $command->getSlipNo(),
            $command->getChequeNo(),
            $command->getChequeDate()
        );

        $receiptDate = new \DateTime($command->getReceiptDate());
        $chequeDate = new \DateTime($command->getChequeDate());
        $feeStatusRef = $this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID);
        $paymentMethodRef = $this->getRepo()->getRefdataReference(FeeEntity::METHOD_CHEQUE);

        // update fee records as paid
        foreach ($fees as $fee) {
            $fee
                ->setFeeStatus($feeStatusRef)
                ->setReceivedDate($receiptDate)
                ->setReceiptNo($response['receipt_reference'])
                ->setPaymentMethod($paymentMethodRef)
                ->setPayerName($command->getPayer())
                ->setPayingInSlipNumber($command->getSlipNo())
                ->setReceivedAmount($fee->getAmount())
                ->setChequePoNumber($command->getChequeNo())
                ->setChequePoDate($chequeDate);

            $this->getRepo('Fee')->save($fee);

            // trigger side effects
            $result->merge(
                $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
            );
        }

        $result->addMessage('Fee(s) updated as Paid by cheque');

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
    protected function poPayment($customerReference, $command, $fees, $result)
    {
        $this->checkAmountMatchesTotalDue($command->getReceived(), $fees);

        // fire off to CPMS to record payment
        $response = $this->cpmsHelper->recordPostalOrderPayment(
            $fees,
            $customerReference,
            $command->getReceived(),
            $command->getReceiptDate(),
            $command->getPayer(),
            $command->getSlipNo(),
            $command->getPoNo()
        );

        $receiptDate = new \DateTime($command->getReceiptDate());
        $feeStatusRef = $this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID);
        $paymentMethodRef = $this->getRepo()->getRefdataReference(FeeEntity::METHOD_POSTAL_ORDER);

        // update fee records as paid
        foreach ($fees as $fee) {
            $fee
                ->setFeeStatus($feeStatusRef)
                ->setReceivedDate($receiptDate)
                ->setReceiptNo($response['receipt_reference'])
                ->setPaymentMethod($paymentMethodRef)
                ->setPayerName($command->getPayer())
                ->setPayingInSlipNumber($command->getSlipNo())
                ->setReceivedAmount($fee->getAmount())
                ->setChequePoNumber($command->getPoNo());

            $this->getRepo('Fee')->save($fee);

            // trigger side effects
            $result->merge(
                $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
            );
        }

        $result->addMessage('Fee(s) updated as Paid by postal order');

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
                    'paymentMethod' => $fee->getPaymentMethod()->getId(),
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

     /**
      * @todo share with AppOutstandingFeesQuery
      * Get fees pertaining to the application
      *
      * AC specify we should only get the *latest* application and interim
      * fees in the event there are multiple fees outstanding.
      */
    protected function getOutstandingFeesForApplication($applicationId)
    {
        $outstandingFees = [];
        $applicationFee = $this->getLatestOutstandingApplicationFeeForApplication($applicationId);

        if ($applicationFee) {
            $outstandingFees[] = $applicationFee;
        }

        // @TODO get the interim fee as well

        return $outstandingFees;
    }

    protected function getLatestOutstandingApplicationFeeForApplication($applicationId)
    {
        $application = $this->getRepo('Application')->fetchById($applicationId);

        $applicationType = $application->getApplicationType();
        $feeTypeFeeTypeId = ($applicationType == ApplicationEntity::APPLICATION_TYPE_VARIATION)
            ? FeeTypeEntity::FEE_TYPE_VAR
            : FeeTypeEntity::FEE_TYPE_APP;

        $applicationDate = new \DateTime($application->getApplicationDate());

        $feeType = $this->getRepo('FeeType')->fetchLatest(
            $this->getRepo()->getRefdataReference($feeTypeFeeTypeId),
            $application->getGoodsOrPsv(),
            $application->getLicenceType(),
            $applicationDate,
            $application->getLicence()->getTrafficArea()
        );

        return $this->getRepo('Fee')->fetchLatestFeeByTypeStatusesAndApplicationId(
            $feeType->getId(),
            [FeeEntity::STATUS_OUTSTANDING, FeeEntity::STATUS_WAIVE_RECOMMENDED],
            $application->getId()
        );
    }
}
