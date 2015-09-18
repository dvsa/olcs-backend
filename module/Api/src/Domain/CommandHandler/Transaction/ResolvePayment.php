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

        $cpmsStatus = $this->getCpmsService()->getPaymentStatus($transaction->getReference());

        $now = new DateTime();

        $result = new Result();

        switch ($cpmsStatus) {
            case Cpms::PAYMENT_SUCCESS:
                $status = Transaction::STATUS_PAID;
                $transaction
                    ->setCompletedDate($now)
                    ->setProcessedByUser($this->getCurrentUser());
                $feeStatusRef = $this->getRepo()->getRefdataReference(Fee::STATUS_PAID);
                foreach ($transaction->getFeeTransactions() as $ft) {
                    $fee = $ft->getFee();
                    $fee->setFeeStatus($feeStatusRef);
                    $this->getRepo('Fee')->save($fee);
                    // trigger side effects
                    $result->merge(
                        $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
                    );
                }
                break;
            case Cpms::PAYMENT_FAILURE:
                $status = Transaction::STATUS_FAILED;
                break;
            case Cpms::PAYMENT_CANCELLATION:
                $status = Transaction::STATUS_CANCELLED;
                break;
            case Cpms::PAYMENT_IN_PROGRESS:
            case Cpms::PAYMENT_GATEWAY_REDIRECT_URL_RECEIVED:
                // resolve any abandoned payments as 'failed'
                $status = Transaction::STATUS_FAILED;
                break;
            default:
                throw new ValidationException(['Unknown CPMS payment_status: '.$cpmsStatus]);
        }

        $transaction->setStatus($this->getRepo()->getRefdataReference($status));
        $this->getRepo()->save($transaction);

        $result->addId('transaction', $transaction->getId());
        $result->addMessage('Transaction resolved as '. $transaction->getStatus()->getDescription());

        return $result;
    }
}
