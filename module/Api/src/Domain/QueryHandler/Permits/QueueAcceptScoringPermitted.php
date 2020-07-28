<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted as QueueAcceptScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Queue accept scoring permitted
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class QueueAcceptScoringPermitted extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermitStock';

    /**
     * Handle query
     *
     * @param QueryInterface|QueueAcceptScoringPermittedQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockId = $query->getId();
        $stock = $this->getRepo()->fetchById($stockId);

        if (!$stock->statusAllowsQueueAcceptScoring()) {
            return [
                'result' => false,
                'message' => sprintf(
                    'Acceptance is not permitted when stock status is \'%s\'',
                    $stock->getStatusDescription()
                )
            ];
        }

        return $this->getQueryHandler()->handleQuery(
            CheckAcceptScoringPrerequisites::create(['id' => $stockId])
        );
    }
}
