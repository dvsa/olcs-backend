<?php

/**
 * Resolve Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\CpmsHelperService as Cpms;

/**
 * Resolve Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @todo rename this?
 */
final class ResolvePayment extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Fee'];

    /**
     * @var \Dvsa\Olcs\Api\Service\CpmsHelperService $cpmsHelper
     */
    protected $cpmsHelper;

    public function handleCommand(CommandInterface $command)
    {
        /* @var $transaction Transaction */
        $transaction = $this->getRepo()->fetchUsingId($command);

        $cpmsStatus = $this->cpmsHelper->getPaymentStatus($transaction->getReference());

        $now = new \DateTime();

        $result = new Result();

        switch ($cpmsStatus) {
            case Cpms::PAYMENT_SUCCESS:
                $status = Transaction::STATUS_PAID;
                $transaction->setCompletedDate($now);
                $feeStatusRef = $this->getRepo()->getRefdataReference(Fee::STATUS_PAID);
                foreach ($transaction->getFeeTransactions() as $ft) {
                    // $ft->setAmount($fee->getAmount()); // @todo check this is populated when ft is created
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

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->cpmsHelper = $serviceLocator->getServiceLocator()->get('CpmsHelperService');
        return $this;
    }
}
