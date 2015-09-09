<?php

/**
 * Pay Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Pay Outstanding Fees
 * (initiates a CPMS payment which is a two-step process)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class PayOutstandingFees extends AbstractCommandHandler implements
    TransactionedInterface, AuthAwareInterface, CpmsAwareInterface
{
    use AuthAwareTrait, CpmsAwareTrait;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Fee'];

    /**
     * There are three valid use cases for this command
     *  - organisationId AND feeIds
     *  - applicationId only
     *  - feeIds only
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        // @todo remove
        // $result->addMessage('Using API helper version ' . $this->getCpmsService()->getVersion());

        if (!empty($command->getOrganisationId())) {
            $fees = $this->getOutstandingFeesForOrganisation($command);
        } elseif (!empty($command->getApplicationId())) {
            $fees = $this->feesHelper->getOutstandingFeesForApplication($command->getApplicationId());
        } else {
            $fees = $this->getRepo('Fee')->fetchOutstandingFeesByIds($command->getFeeIds());
        }

        // filter out fees that may have been paid by resolving outstanding payments
        $feesToPay = $this->resolvePaidFees($fees, $result);

        if (empty($feesToPay)) {
            $result->addMessage('No fees to pay');
            return $result;
        }

        try {
            switch ($command->getPaymentMethod()) {
                case FeeEntity::METHOD_CARD_ONLINE:
                case FeeEntity::METHOD_CARD_OFFLINE:
                    return $this->cardPayment($command, $feesToPay, $result);
                case FeeEntity::METHOD_CASH:
                case FeeEntity::METHOD_CHEQUE:
                case FeeEntity::METHOD_POSTAL_ORDER:
                    return $this->immediatePayment($command, $feesToPay, $result);
            }
        } catch (CpmsResponseException $e) {
            // rethrow as Domain exception
            throw new RuntimeException('Error from CPMS service');
        }
    }

    /**
     * @param CommandInterface $command
     * @param array $feesToPay
     * @param Result $result
     *
     * @return Result
     */
    protected function cardPayment($command, $feesToPay, $result)
    {
        // fire off to CPMS
        $response = $this->getCpmsService()->initiateCardRequest(
            $command->getCpmsRedirectUrl(),
            $feesToPay
        );

        // create transaction
        $transaction = new TransactionEntity();
        $transaction
            ->setReference($response['receipt_reference'])
            ->setGatewayUrl($response['gateway_url'])
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_OUTSTANDING))
            ->setType($this->getRepo()->getRefdataReference(TransactionEntity::TYPE_PAYMENT))
            ->setPaymentMethod($this->getRepo()->getRefdataReference($command->getPaymentMethod()));

        // create feeTransaction record(s)
        $feeTransactions = new ArrayCollection();
        $transaction->setFeeTransactions($feeTransactions);
        foreach ($feesToPay as $fee) {
            $feeTransaction = new FeeTransactionEntity();
            $feeTransaction
                ->setFee($fee)
                ->setAmount($fee->getOutstandingAmount())
                ->setTransaction($transaction); // needed for cascade persist to work
            $feeTransactions->add($feeTransaction);
        }

        // persist
        $this->getRepo()->save($transaction);

        $result->addId('transaction', $transaction->getId());
        $result->addMessage('Transaction record created');

        return $result;
    }

    /**
     * @param CommandInterface $command
     * @param array $fees
     * @param Result $result
     *
     * @return Result
     */
    protected function immediatePayment($command, $fees, $result)
    {
        $this->checkAmountMatchesTotalDue($command->getReceived(), $fees);

        // fire off to relevant CPMS method to record payment
        $response = $this->recordPaymentInCpms($command, $fees);

        $receiptDate = new \DateTime($command->getReceiptDate());
        $chequeDate = $command->getChequeDate() ? new \DateTime($command->getChequeDate()) : null;
        $chequePoNumber = $command->getChequeNo() ?: $command->getPoNo();

        // create transaction
        $transaction = new TransactionEntity();
        $transaction
            ->setReference($response['receipt_reference'])
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_PAID))
            ->setType($this->getRepo()->getRefdataReference(TransactionEntity::TYPE_PAYMENT))
            ->setPaymentMethod($this->getRepo()->getRefdataReference($command->getPaymentMethod()))
            ->setCompletedDate($receiptDate)
            ->setPayerName($command->getPayer())
            ->setPayingInSlipNumber($command->getSlipNo())
            ->setProcessedByUser($this->getCurrentUser())
            ->setChequePoDate($chequeDate) // note we don't actually capture date for PO's
            ->setChequePoNumber($chequePoNumber);

        // create feeTransaction record(s)
        $feeTransactions = new ArrayCollection();
        $transaction->setFeeTransactions($feeTransactions);
        foreach ($fees as $fee) {
            $feeTransaction = new FeeTransactionEntity();
            $feeTransaction
                ->setFee($fee)
                ->setAmount($fee->getOutstandingAmount())
                ->setTransaction($transaction); // needed for cascade persist to work
            $feeTransactions->add($feeTransaction);

            // Update fee status, we need to call save() rather than rely on cascade persist
            // at this level of nesting.
            $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID));
            $this->getRepo('Fee')->save($fee);

            // trigger side effects
            $result->merge(
                $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
            );
        }

        // persist
        $this->getRepo()->save($transaction);

        $method = $this->getRepo()->getRefdataReference($command->getPaymentMethod())->getDescription();
        $result
            ->addId('transaction', $transaction->getId())
            ->addMessage('Transaction record created')
            ->addMessage('Fee(s) updated as paid by ' . $method);

        return $result;
    }

    /**
     * @return array|false
     */
    protected function recordPaymentInCpms($command, $fees)
    {
        switch ($command->getPaymentMethod()) {
            case FeeEntity::METHOD_CASH:
                $response = $this->getCpmsService()->recordCashPayment(
                    $fees,
                    $command->getReceived(),
                    $command->getReceiptDate(),
                    $command->getPayer(),
                    $command->getSlipNo()
                );
                break;
            case FeeEntity::METHOD_CHEQUE:
                $response = $this->getCpmsService()->recordChequePayment(
                    $fees,
                    $command->getReceived(),
                    $command->getReceiptDate(),
                    $command->getPayer(),
                    $command->getSlipNo(),
                    $command->getChequeNo(),
                    $command->getChequeDate()
                );
                break;
            case FeeEntity::METHOD_POSTAL_ORDER:
                $response = $this->getCpmsService()->recordPostalOrderPayment(
                    $fees,
                    $command->getReceived(),
                    $command->getReceiptDate(),
                    $command->getPayer(),
                    $command->getSlipNo(),
                    $command->getPoNo()
                );
                break;
            default:
                throw new RuntimeException('invalid payment method: ' . $command->getPaymentMethod());
        }

        return $response;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->feesHelper = $mainServiceLocator->get('FeesHelperService');
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

        foreach ($fee->getFeeTransactions() as $ft) {
            if ($ft->getTransaction()->isOutstanding()) {

                $transactionId = $ft->getTransaction()->getId();

                // resolve outstanding payment
                $dto = ResolvePaymentCommand::create(
                    [
                        'id' => $transactionId,
                        'paymentMethod' => $ft->getTransaction()->getPaymentMethod()->getId(),
                    ]
                );
                $this->getCommandHandler()->handleCommand($dto);

                // check payment status
                $transaction = $this->getRepo()->fetchById($transactionId);
                $result->addMessage(
                    sprintf(
                        'transaction %d resolved as %s',
                        $transactionId,
                        $transaction->getStatus()->getDescription()
                    )
                );

                if ($transaction->isPaid()) {
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
     * @param array $fees
     * return float
     */
    protected function getTotalAmountFromFees($fees)
    {
        $totalAmount = 0;
        foreach ($fees as $fee) {
            $totalAmount += (float)$fee->getOutstandingAmount();
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
        $amount    = $this->getCpmsService()->formatAmount($amount);
        $totalFees = $this->getCpmsService()->formatAmount($this->getTotalAmountFromFees($fees));
        if ($amount !== $totalFees) {
            throw new ValidationException(["Amount must match the fee(s) due"]);
        }
    }

    protected function getOutstandingFeesForOrganisation(CommandInterface $command)
    {
        // get outstanding fees for organisation
        $outstandingFees = $this->getRepo('Fee')
                ->fetchOutstandingFeesByOrganisationId($command->getOrganisationId());

        // filter requested fee ids against outstanding fees
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
}
