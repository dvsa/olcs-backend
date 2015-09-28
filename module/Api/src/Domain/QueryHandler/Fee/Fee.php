<?php

/**
 * Fee
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepository;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;

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
            'hasOutstandingWaiveTransaction' => !empty($fee->getOutstandingWaiveTransaction()),
        ];
    }
}
