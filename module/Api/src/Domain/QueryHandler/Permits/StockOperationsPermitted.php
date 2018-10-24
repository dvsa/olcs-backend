<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted as QueueRunScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted as QueueAcceptScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Stock operations permitted
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockOperationsPermitted extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    /**
     * Handle query
     *
     * @param QueryInterface|StockOperationsPermittedQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockIdParams = ['id' => $query->getId()];

        $queryHandler = $this->getQueryHandler();
        $scoringResult = $queryHandler->handleQuery(QueueRunScoringPermittedQuery::create($stockIdParams));
        $acceptResult = $queryHandler->handleQuery(QueueAcceptScoringPermittedQuery::create($stockIdParams));

        return [
            'scoringPermitted' => $scoringResult['result'],
            'acceptPermitted' => $acceptResult['result']
        ];
    }
}
