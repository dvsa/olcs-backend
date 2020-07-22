<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Transfer\Query\Permits\StockOperationsPermitted as StockOperationsPermittedQuery;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted as QueueRunScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted as QueueAcceptScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\Query\Permits\DeviationData as DeviationDataQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Stock operations permitted
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockOperationsPermitted extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermitStock';

    protected $extraRepos = ['IrhpApplication'];

    /**
     * Handle query
     *
     * @param QueryInterface|StockOperationsPermittedQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockId = $query->getId();

        $stock = $this->getRepo()->fetchById($stockId);
        $stockStatus = $stock->getStatus();
        $stockIdParams = ['id' => $stockId];

        $queryHandler = $this->getQueryHandler();
        $scoringResult = $queryHandler->handleQuery(QueueRunScoringPermittedQuery::create($stockIdParams));
        $acceptResult = $queryHandler->handleQuery(QueueAcceptScoringPermittedQuery::create($stockIdParams));

        $sourceValues = $this->getRepo('IrhpApplication')->fetchDeviationSourceValues($stockId);
        $deviationData = $this->getQueryHandler()->handleQuery(
            DeviationDataQuery::create(['sourceValues' => $sourceValues])
        );

        return [
            'stockStatusId' => $stockStatus->getId(),
            'stockStatusMessage' => $stockStatus->getDescription(),
            'scoringPermitted' => $scoringResult['result'],
            'scoringMessage' => $scoringResult['message'],
            'acceptPermitted' => $acceptResult['result'],
            'acceptMessage' => $acceptResult['message'],
            'meanDeviation' => $deviationData['meanDeviation']
        ];
    }
}
