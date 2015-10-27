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
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as CreateFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Service\CpmsResponseException;
use Dvsa\Olcs\Api\Service\Exception as ServiceException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Transfer\Command\Fee\RejectWaive as RejectWaiveCmd;

/**
 * Pay Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class PayOutstandingFees extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    CpmsAwareInterface
{
    use AuthAwareTrait, CpmsAwareTrait;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Fee', 'FeeType'];

    /**
     * There are three valid use cases for this command
     *  - organisationId AND feeIds
     *  - applicationId only
     *  - feeIds only
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

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
            $cardMethods = [FeeEntity::METHOD_CARD_ONLINE, FeeEntity:: METHOD_CARD_OFFLINE];
            if (in_array($command->getPaymentMethod(), $cardMethods)) {
                return $this->cardPayment($command, $feesToPay, $result);
            } else {
                return $this->immediatePayment($command, $feesToPay, $result);
            }
        } catch (CpmsResponseException $e) {
            // rethrow as Domain exception
            throw new RuntimeException(
                'Error from CPMS service: ' . json_encode($e->getResponse()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Initiates a CPMS card payment which is a two-step process
     *
     * @param CommandInterface $command
     * @param array $feesToPay
     * @param Result $result
     *
     * @return Result
     */
    protected function cardPayment($command, $feesToPay, $result)
    {
        // fire off to CPMS
        if ($command->getPaymentMethod() === FeeEntity::METHOD_CARD_OFFLINE) {
            $response = $this->getCpmsService()->initiateCnpRequest($command->getCpmsRedirectUrl(), $feesToPay);
        } else {
            $response = $this->getCpmsService()->initiateCardRequest($command->getCpmsRedirectUrl(), $feesToPay);
        }

        // create transaction
        $transaction = new TransactionEntity();
        $transaction
            ->setReference($response['receipt_reference'])
            ->setGatewayUrl($response['gateway_url'])
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_OUTSTANDING))
            ->setType($this->getRepo()->getRefdataReference(TransactionEntity::TYPE_PAYMENT))
            ->setPaymentMethod($this->getRepo()->getRefdataReference($command->getPaymentMethod()));

        // create feeTransaction record(s) and cancel any pending waives
        $feeTransactions = new ArrayCollection();
        $transaction->setFeeTransactions($feeTransactions);
        foreach ($feesToPay as $fee) {

            $result->merge($this->maybeCancelPendingWaive($fee));

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
     * Cash/cheque/PO payment
     *
     * @param CommandInterface $command
     * @param array $fees
     * @param Result $result
     *
     * @return Result
     */
    protected function immediatePayment($command, $fees, $result)
    {
        $this->validateAmount($command->getReceived(), $fees);

        // work out the allocation of the payment amount to fees, will create
        // balancing entry to handle any overpayment
        $allocations = $this->allocatePayments($command->getReceived(), $fees, $result);

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

        // create feeTransaction record(s) and cancel any pending waives
        foreach ($fees as $fee) {

            $result->merge($this->maybeCancelPendingWaive($fee));

            $allocatedAmount = $allocations[$fee->getId()];
            $markAsPaid = ($allocatedAmount === $fee->getOutstandingAmount());
            $feeTransaction = new FeeTransactionEntity();
            $feeTransaction
                ->setFee($fee)
                ->setAmount($allocatedAmount)
                ->setTransaction($transaction); // needed for cascade persist to work
            $transaction->getFeeTransactions()->add($feeTransaction);

            if ($markAsPaid) {
                $fee->setFeeStatus($this->getRepo()->getRefdataReference(FeeEntity::STATUS_PAID));
                $method = $this->getRepo()->getRefdataReference($command->getPaymentMethod())->getDescription();
                $result->addMessage('Fee ID ' . $fee->getId() . ' updated as paid by ' . $method);
                // We need to call save() on the fee, it won't cascade persist from the transaction
                $this->getRepo('Fee')->save($fee);
                $result->merge($this->handleSideEffect(PayFeeCmd::create(['id' => $fee->getId()])));
            } elseif ($fee->getLicence()) {
                // Generate Insufficient Fee Request letter
                $result->merge($this->generateInsufficientFeeRequestLetter($fee, $allocatedAmount));
            }
        }

        // persist transaction
        $this->getRepo()->save($transaction);

        $result
            ->addId('transaction', $transaction->getId())
            ->addMessage('Transaction record created: ' . $transaction->getReference())
            ->addId('feeTransaction', $transaction->getFeeTransactionIds())
            ->addMessage('FeeTransaction record(s) created');

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
        $this->feesHelper = $serviceLocator->getServiceLocator()->get('FeesHelperService');
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
                $result->merge($this->getCommandHandler()->handleCommand($dto));

                // check payment status
                $transaction = $this->getRepo()->fetchById($transactionId);

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
     * Partial payments are supported for cash/cheque/PO payments but amount
     * must not result in a zero allocation to any fee.
     * The form validation will normally catch any mismatch but it relies on a
     * hidden field so we have a secondary check here in the service layer.
     *
     * @param string $amount
     * @param array $fees
     * @return null
     * @throws ValidationException
     */
    protected function validateAmount($amount, $fees)
    {
        $minAmount = $this->feesHelper->getMinPaymentForFees($fees);
        if ($amount < $minAmount) {
            throw new ValidationException([sprintf("Amount must be at least %1\$.2f", $minAmount)]);
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

    /**
     * @param string $receivedAmount
     * @param array $fees - passed by reference as we may need to append
     * @param Result $result
     * @return array
     */
    protected function allocatePayments($receivedAmount, &$fees, $result)
    {
        $feeResult = $this->maybeCreateOverpaymentFee($receivedAmount, $fees);

        if ($feeResult->getId('fee')) {
            // an overpayment balancing fee was created, add it to the list
            $fees[] = $this->getRepo('Fee')->fetchById($feeResult->getId('fee'));
        }

        $result->merge($feeResult);

        try {
            // work out the allocation of the payment amount to fees
            $allocations = $this->feesHelper->allocatePayments($receivedAmount, $fees);
        } catch (ServiceException $e) {
            // if there is an allocation error, rethrow as Domain exception
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $allocations;
    }

    /**
     * @param string $receivedAmount
     * @param array $fees
     * @return Result
     */
    protected function maybeCreateOverpaymentFee($receivedAmount, $fees)
    {
        $overpaymentAmount = $this->feesHelper->getOverpaymentAmount($receivedAmount, $fees);

        if ($overpaymentAmount > 0) {

            // sort fees
            $fees = $this->feesHelper->sortFeesByInvoiceDate($fees);

            // get IDs for description
            $feeIds = array_map(
                function ($fee) {
                    return $fee->getId();
                },
                $fees
            );

            // we get licenceId, applicationId, busRegId, irfoGvPermitId,from the first existing fee
            $existingFee = reset($fees);
            $ids = $this->getIdsFromFee($existingFee);

            // get correct feeType
            $feeType = $this->getRepo('FeeType')->fetchLatestForOverpayment();

            $dtoData = array_merge(
                [
                    'amount'       => $overpaymentAmount,
                    'invoicedDate' => (new DateTime())->format(\DateTime::W3C),
                    'feeType'      => $feeType->getId(),
                    'description'  => 'Overpayment on fees: ' . implode(', ', $feeIds),
                ],
                $ids
            );

            return $this->handleSideEffect(CreateFeeCmd::create($dtoData));
        }

        // if no overpayment, return empty result
        return new Result();
    }

    /**
     * @param FeeEntity $existingFee
     * @return array
     */
    private function getIdsFromFee($existingFee)
    {
        $licenceId = null;
        if ($existingFee->getLicence()) {
            $licenceId = $existingFee->getLicence()->getId();
        }

        $applicationId = null;
        if ($existingFee->getApplication()) {
            $applicationId = $existingFee->getApplication()->getId();
        }

        $busRegId = null;
        if ($existingFee->getBusReg()) {
            $busRegId = $existingFee->getBusReg()->getId();
        }

        $irfoGvPermitId = null;
        if ($existingFee->getIrfoGvPermit()) {
            $irfoGvPermitId = $existingFee->getIrfoGvPermit()->getId();
        }

        return [
            'licence'      => $licenceId,
            'application'  => $applicationId,
            'busReg'       => $busRegId,
            'irfoGvPermit' => $irfoGvPermitId,
        ];
    }

    private function generateInsufficientFeeRequestLetter(FeeEntity $fee, $allocatedAmount)
    {

        // we need to calculate actual outstanding fee, because new transaction is not saved
        // on this step, so $fee->getOutstandingFeeAmount() will return the previous value
        $actualOutstandingAmount = $fee->getOutstandingAmount() - $allocatedAmount;
        $receivedAmount = $fee->getAmount() - $actualOutstandingAmount;

        $dtoData = [
            'template' => 'FEE_REQ_INSUFFICIENT',
            'query' => [
                'fee' => $fee->getId(),
                'licence' => $fee->getLicence()->getId()
            ],
            'knownValues' => [
                'INSUFFICIENT_FEE_TABLE' => [
                    'receivedAmount' => $receivedAmount,
                    'outstandingAmount' => $actualOutstandingAmount
                ]
            ],
            'description' => 'Insufficient Fee Request',
            'licence'     => $fee->getLicence()->getId(),
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_FEE_REQUEST,
            'isExternal'  => false,
            'dispatch'    => true
        ];
        if ($fee->getApplication()) {
            $dtoData['application'] = $fee->getApplication()->getId();
        }

        return $this->handleSideEffect(GenerateAndStore::create($dtoData));
    }

    /**
     * If there is an outstanding waive transaction for a fee, reject it
     */
    private function maybeCancelPendingWaive(FeeEntity $fee)
    {
        $result = new Result();

        if ($fee->getOutstandingWaiveTransaction()) {
            $rejectCmd = RejectWaiveCmd::create(['id' => $fee->getId()]);
            $result->merge($this->handleSideEffect($rejectCmd));
        }

        return $result;
    }
}
