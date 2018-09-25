<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Transfer\Query\Permits\StockScoringPermitted as StockScoringPermittedQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use DateTime;

/**
 * Stock scoring permitted
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockScoringPermitted extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitStock';

    protected $extraRepos = ['IrhpPermitWindow'];

    /**
     * Handle query
     *
     * @param QueryInterface|StockScoringPermittedQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockId = $query->getId();

        $lastOpenWindow = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByStockId($stockId);
        if (!is_null($lastOpenWindow)) {
            $permitted = false;
        } else {
            $stock = $this->getRepo()->fetchById($query->getId());

            $permitted = in_array(
                $stock->getStatus()->getId(),
                [
                    IrhpPermitStock::STATUS_SCORING_NEVER_RUN,
                    IrhpPermitStock::STATUS_SCORING_SUCCESSFUL,
                    IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL,
                    IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL
                ]
            );
        }

        return ['result' => $permitted];
    }
}
