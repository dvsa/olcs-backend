<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Cli\Domain\Query\Permits\StockAvailability as StockAvailabilityQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Stock availability
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockAvailability extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermitRange';

    protected $extraRepos = ['IrhpPermit'];

    /**
     * Handle query
     *
     * @param QueryInterface|StockAvailabilityQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $combinedRangeSize = $this->getRepo()->getCombinedRangeSize($query->getStockId());
        if (is_null($combinedRangeSize)) {
            return [
                'result' => false,
                'message' => 'No ranges available for this stock'
            ];
        }

        $assignedPermits = $this->getRepo('IrhpPermit')->getPermitCount($query->getStockId());
        $permitsAvailable = $combinedRangeSize - $assignedPermits;

        if ($permitsAvailable <= 0) {
            return [
                'result' => false,
                'message' => 'No permits available within the ranges of this stock'
            ];
        }

        return [
            'result' => true,
            'message' => null
        ];
    }
}
