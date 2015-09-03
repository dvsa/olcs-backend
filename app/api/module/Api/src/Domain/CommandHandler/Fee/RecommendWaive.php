<?php

/**
 * Recommend Waive
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Recommend Waive
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class RecommendWaive extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $now = new DateTime();

        /** @var FeeEntity $fee */
        $fee = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $transaction = new TransactionEntity();
        $transaction
            ->setType($this->getRepo()->getRefdataReference(TransactionEntity::TYPE_WAIVE))
            ->setStatus($this->getRepo()->getRefdataReference(TransactionEntity::STATUS_OUTSTANDING))
            ->setPaymentMethod($this->getRepo()->getRefdataReference(FeeEntity::METHOD_WAIVE))
            ->setComment($command->getWaiveReason())
            ->setWaiveRecommendationDate($now)
            ->setWaiveRecommenderUser($this->getCurrentUser());

        $feeTransaction = new FeeTransactionEntity();
        $feeTransaction
            ->setFee($fee)
            ->setTransaction($transaction);
        $fee->getFeeTransactions()->add($feeTransaction);

        // save fee will cascade persist
        $this->getRepo()->save($fee);

        $result
            ->addId('fee', $fee->getId())
            ->addMessage('Fee updated')
            ->addId('transaction', $transaction->getId())
            ->addMessage('Waive transaction created');

        return $result;
    }
}
