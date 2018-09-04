<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Cli\Domain\Query\Permits\StockLackingRandomisedScore as StockLackingRandomisedScoreQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Stock lacking randomised score
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockLackingRandomisedScore extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpCandidatePermit';

    /**
     * Handle query
     *
     * @param QueryInterface|StockLackingRandomisedScoreQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $permitCount = $this->getRepo()->getCountLackingRandomisedScore(
            $query->getStockId()
        );

        return ['result' => ($permitCount > 0)];
    }
}
