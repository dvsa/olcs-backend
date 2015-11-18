<?php

/**
 * Get latest fee type
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get latest fee type
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetLatestFeeType extends AbstractQueryHandler
{
    protected $repoServiceName = 'FeeType';

    public function handleQuery(QueryInterface $query)
    {
        $feeType = $this->getRepo('FeeType')->fetchLatest(
            $this->getRepo()->getRefdataReference($query->getFeeType()),
            $this->getRepo()->getRefdataReference($query->getOperatorType()),
            $this->getRepo()->getRefdataReference($query->getLicenceType()),
            new \DateTime($query->getDate()),
            $query->getTrafficArea()
        );

        return [
            'result' => [$feeType],
            'count' => 1
        ];
    }
}
