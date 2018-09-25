<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\StockScoringPermitted as StockScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\Query\Permits\StockAcceptPermitted as StockAcceptPermittedQuery;
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

        $scoringResult = $queryHandler->handleQuery(StockScoringPermittedQuery::create($stockIdParams));
        $acceptResult = $queryHandler->handleQuery(StockAcceptPermittedQuery::create($stockIdParams));

        return [
            'scoringPermitted' => $scoringResult['result'],
            'acceptPermitted' => $acceptResult['result']
        ];
    }
}
