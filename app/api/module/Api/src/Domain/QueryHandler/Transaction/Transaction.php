<?php

/**
 * Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Transaction;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as Entity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;

/**
 * Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Transaction extends AbstractQueryHandler
{
    protected $repoServiceName = 'Transaction';

    public function handleQuery(QueryInterface $query)
    {
        $transaction = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $transaction,
            [
                'feeTransactions' => [
                    'fee',
                ],
                'processedByUser',
                'waiveRecommenderUser',
            ],
            [
                'fees' => $this->flattenFees($transaction),
            ]
        );
    }

    /**
     * Get a flattened array of fee data for the transaction.
     */
    protected function flattenFees($transaction)
    {
        $fees = [];

        $transaction->getFeeTransactions()->forAll(
            function ($key, $ft) use (&$fees) {
                unset($key); // unused
                $fee = $ft->getFee()->serialize(['feeStatus']);
                $fee['reversingTransaction'] = $this->getReversingTransactionData($ft);
                $id = $fee['id'];
                if (isset($fees[$id])) {
                    $fees[$id]['allocatedAmount'] += $ft->getAmount();
                } else {
                    $fee['allocatedAmount'] = $ft->getAmount();
                    $fees[$id] = $fee;
                }

                return true;
            }
        );

        // Sort as per AC for OLCS-10458:
        // 'A list of fees in chronological order (i.e. in fee id order) with the newest at the bottom.'
        uasort(
            $fees,
            function ($a, $b) {
                return ($a['id'] < $b ['id']) ? -1 : 1;
            }
        );

        return $fees;
    }

    /**
     * @return array|null
     */
    protected function getReversingTransactionData(FeeTransactionEntity $ft)
    {
        if (count($ft->getReversingFeeTransactions())>0) {;
            $reversal = $ft->getReversingFeeTransactions()->first();
            return [
                'id' => $reversal->getTransaction()->getId(),
                'type' => $reversal->getTransaction()->getType()->getId(),
            ];
        }
    }
}
