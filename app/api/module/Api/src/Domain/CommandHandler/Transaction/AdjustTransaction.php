<?php

/**
 * Adjust Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

// use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
// use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
// use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
// use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
// use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
// use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
// use Dvsa\Olcs\Api\Service\CpmsResponseException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Adjust Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class AdjustTransaction extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    CpmsAwareInterface
{
    use AuthAwareTrait, CpmsAwareTrait;

    protected $repoServiceName = 'Transaction';

    public function handleCommand(CommandInterface $command)
    {
        $originalTransaction = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($command, $originalTransaction);

        // try {
        //     $fee = $originalTransaction->getFeeTransactions()->first()->getFee();
        //     $response = $this->getCpmsService()->reversePayment(
        //         $originalTransaction->getReference(),
        //         $originalTransaction->getPaymentMethod()->getId(),
        //         [$fee]
        //     );
        // } catch (CpmsResponseException $e) {
        //     // rethrow as Domain exception
        //     throw new RuntimeException(
        //         'Error from CPMS service: ' . json_encode($e->getResponse()),
        //         $e->getCode(),
        //         $e
        //     );
        // }

        $this->result->addMessage('OK');
        return $this->result;
    }

    private function validate(CommandInterface $command, TransactionEntity $transaction)
    {
        $changes = [];

        if ($command->getReceived() != $transaction->getTotalAmount()) {
            $changes[] = 'received';
        }
        if ($command->getPayer() != $transaction->getPayerName()) {
            $changes[] = 'payer';
        }
        if ($command->getSlipNo() != $transaction->getPayingInSlipNumber()) {
            $changes[] = 'slipNo';
        }
        if ($command->getChequeNo() && $command->getChequeNo() != $transaction->getChequePoNumber()) {
            $changes[] = 'chequeNo';
        }
        if ($command->getPoNo() && $command->getPoNo() != $transaction->getChequePoNumber()) {
            $changes[] = 'poNo';
        }
        if ($command->getChequeDate() != $transaction->getChequePoDate()) {
            $changes[] = 'chequeDate';
        }

        if (empty($changes)) {
            throw new ValidationException(['You haven\'t changed anything']);
        }
    }
}
