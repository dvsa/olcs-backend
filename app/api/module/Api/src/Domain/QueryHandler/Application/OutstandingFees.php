<?php

/**
 * Application - Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Dvsa\Olcs\Api\Domain\ApplicationOutstandingFeesTrait;

/**
 * Application - Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class OutstandingFees extends AbstractQueryHandler
{
    use ApplicationOutstandingFeesTrait;

    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Fee', 'FeeType'];

    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo('Application')->fetchUsingId($query);

        $outstandingFees = $this->getOutstandingFeesForApplication($application->getId());

        return $this->result(
            $application,
            [],
            [
                'outstandingFeeTotal' => $this->totalFees($outstandingFees),
                'outstandingFees' => $this->resultList(
                    $outstandingFees,
                    [
                        'feeType',
                        'feeStatus',
                        'feePayments' => [
                            'payment',
                        ]
                    ]
                ),
            ]
        );
    }

    /**
     * @param array $outstandingFees
     * @return string formatted amount (1234.56)
     */
    protected function totalFees($outstandingFees)
    {
        $total = 0;

        if (is_array($outstandingFees)) {
            foreach ($outstandingFees as $fee) {
                $total += $fee->getAmount();
            }
        }

        return number_format($total, 2, null, null);
    }
}
