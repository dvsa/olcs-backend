<?php

/**
 * Resolve Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as Cpms;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Resolve Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class ResolvePayment extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    CpmsAwareInterface
{
    use AuthAwareTrait, CpmsAwareTrait;

    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Fee'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $transaction Transaction */
        $transaction = $this->getRepo()->fetchUsingId($command);

        $result = new Result();

        if ($transaction->isWaive()) {
            $result->addMessage(sprintf('Waive transaction %d not resolved', $transaction->getId()));
            return $result;
        }

        $cpmsStatus = $this->getCpmsService()->getPaymentStatus($transaction->getReference());

        $now = new DateTime();

        $transaction
            ->setCompletedDate($now)
            ->setProcessedByUser($this->getCurrentUser());

        switch ($cpmsStatus) {
            case Cpms::PAYMENT_SUCCESS:
                $transaction->setStatus($this->getRepo()->getRefdataReference(Transaction::STATUS_PAID));
                $result->merge($this->updateFees($transaction));
                break;
            case Cpms::PAYMENT_FAILURE:
                $transaction->setStatus($this->getRepo()->getRefdataReference(Transaction::STATUS_FAILED));
                break;
            case Cpms::PAYMENT_CANCELLATION:
                $transaction->setStatus($this->getRepo()->getRefdataReference(Transaction::STATUS_CANCELLED));
                break;
            case Cpms::PAYMENT_IN_PROGRESS:
            case Cpms::PAYMENT_GATEWAY_REDIRECT_URL_RECEIVED:
            case Cpms::PAYMENT_GATEWAY_ERROR:
                // resolve any abandoned payments as 'failed'
                $transaction->setStatus($this->getRepo()->getRefdataReference(Transaction::STATUS_FAILED));
                break;
            default:
                throw new ValidationException(['Unknown CPMS payment_status: '.$cpmsStatus]);
        }

        $this->getRepo()->save($transaction);

        $result->addId('transaction', $transaction->getId());
        $result->addMessage(
            sprintf(
                'Transaction %d resolved as %s',
                $transaction->getId(),
                $transaction->getStatus()->getDescription()
            )
        );

        return $result;
    }

    /**
     * Update fees that may now have been paid in full by a completed transaction
     *
     * @param Transaction
     * @return Result
     */
    protected function updateFees($transaction)
    {
        $result = new Result();

        $paidStatusRef = $this->getRepo()->getRefdataReference(Fee::STATUS_PAID);

        foreach ($transaction->getFeeTransactions() as $ft) {
            $fee = $ft->getFee();
            $outstanding = Fee::amountToPence($fee->getOutstandingAmount()); // convert to integer pence for comparison
            if ($outstanding <= 0) {
                $fee->setFeeStatus($paidStatusRef);
                $this->getRepo('Fee')->save($fee);
                $result->addMessage('Fee ID ' . $fee->getId() . ' updated as paid');
                // trigger side effects
                $result->merge(
                    $this->handleSideEffect(PayFeeCmd::create(['id' => $fee->getId()]))
                );
            }
        }

        return $result;
    }
}
