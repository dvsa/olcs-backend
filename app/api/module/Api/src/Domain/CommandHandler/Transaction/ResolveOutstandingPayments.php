<?php

/**
 * Resolve Outstanding Payments
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Transaction;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as ResolvePaymentCmd;

/**
 * Resolve Outstanding Payments
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class ResolveOutstandingPayments extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Transaction';

    protected $extraRepos = ['Fee'];

    public function handleCommand(CommandInterface $command)
    {
        // @todo get this from config or command?
        $minAge = 30; // minutes
        $transactions = $this->getRepo()->fetchOutstandingCardPayments($minAge);

        /* @var $transaction Transaction */
        foreach ($transactions as $transaction) {
            var_dump($transaction->getId());
            // $cmd = ResolvePaymentCmd::create(['id' => $transaction->getId()]);
            // $this->result->merge($this->handleSideEffect($cmd));
        }

        return $this->result;
    }
}
