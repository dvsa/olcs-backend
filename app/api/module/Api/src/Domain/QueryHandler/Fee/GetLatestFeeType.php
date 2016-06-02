<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get latest fee type
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @todo I think it's not in use and should be removed together with FeeTypeDataService, ApplicationProcessingService
 * @todo and BusProcessingService
 */
class GetLatestFeeType extends AbstractQueryHandler
{
    protected $repoServiceName = 'FeeType';

    /**
     * @param \Dvsa\Olcs\Transfer\Query\Fee\GetLatestFeeType $query
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\FeeType $repo */
        $repo = $this->getRepo('FeeType');

        $feeType = $repo->fetchLatest(
            $repo->getRefdataReference($query->getFeeType()),
            $repo->getRefdataReference($query->getOperatorType()),
            $repo->getRefdataReference($query->getLicenceType()),
            new \DateTime($query->getDate()),
            $query->getTrafficArea()
        );

        return [
            'result' => [$feeType],
            'count' => 1,
        ];
    }
}
