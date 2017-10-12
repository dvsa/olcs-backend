<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepository;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;

/**
 * Fee
 */
class Fee extends AbstractQueryHandler
{
    protected $repoServiceName = 'Fee';

    public function handleQuery(QueryInterface $query)
    {
        /** @var FeeRepository $repo */
        $repo = $this->getRepo();
        $repo->disableSoftDeleteable();

        $fee = $repo->fetchUsingId($query);

        return $this->result(
            $fee,
            [
                'feeTransactions' => [
                    'transaction' => [
                        'paymentMethod',
                        'processedByUser',
                        'status',
                        'type',
                    ],
                ],
            ],
            $this->getAdditionalFeeData($fee)
        );
    }

    /**
     * @param FeeEntity $fee
     * @return array
     */
    private function getAdditionalFeeData(FeeEntity $fee)
    {
        $licence = $fee->getLicence();
        $licenceExpiryDate = ($licence === null) ? null : $licence->getExpiryDate();
        return [
            'allowEdit' => $fee->allowEdit(),
            'outstanding' => $fee->getOutstandingAmount(),

            // fields that the frontend may expect as they were previously
            // on the fee table
            'receiptNo' => $fee->getLatestPaymentRef(),
            'paymentMethod' => $fee->getPaymentMethod(),
            'processedBy' => $fee->getProcessedBy(),
            'payer' => $fee->getPayer(),
            'slipNo' => $fee->getSlipNo(),
            'chequePoNumber' => $fee->getChequePoNumber(),
            'waiveReason' => $fee->getWaiveReason(),
            //////////

            'hasOutstandingWaiveTransaction' => !empty($fee->getOutstandingWaiveTransaction()),
            'canRefund' => $fee->canRefund(),
            'displayTransactions' => $this->getDisplayTransactions($fee),
            'vatInfo' => $this->getVatInfo($fee),
            'licenceExpiryDate' => $licenceExpiryDate,
        ];
    }


    /**
     * Filter and group transaction data for display
     */
    private function getDisplayTransactions(FeeEntity $fee)
    {
        $displayData = [];

        /** @var $ft \Dvsa\Olcs\Api\Entity\Fee\FeeTransaction */
        foreach ($fee->getFeeTransactions() as $ft) {
            $transaction = $ft->getTransaction();
            $id = $transaction->getId();
            if ($transaction->isOutstanding() && $transaction->isWaive()) {
                continue;
            }
            if (isset($displayData[$id])) {
                $displayData[$id]['amount'] += $ft->getAmount();
                continue;
            }
            $displayData[$id] = [
                'transactionId' => $transaction->getId(),
                'type' => $transaction->getType()->getDescription(),
                'completedDate' => $transaction->getCompletedDate(),
                'createdOn' => $transaction->getCreatedOn(),
                'method' => $this->getMethod($transaction),
                'processedBy' => $transaction->getProcessedByFullName(),
                'amount' => $ft->getAmount(),
                'migratedFromOlbs' => $transaction->isMigrated(),
                'status' => $transaction->getStatus(),
            ];
        }

        return $displayData;
    }

    private function getMethod(TransactionEntity $transaction)
    {
        $method = $transaction->getPaymentMethod() ? $transaction->getPaymentMethod()->getDescription() : '';

        return $method . ' ' . $transaction->getDisplayAmount();
    }

    /**
     * @param FeeEntity $fee
     * @return string|null e.g. "20% (S)"
     */
    private function getVatInfo($fee)
    {
        $feeType = $fee->getFeeType();

        if ($feeType->getVatRate() > 0) {
            return sprintf('%d%% (%s)', $feeType->getVatRate(), $feeType->getVatCode());
        }
    }
}
