<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Transfer\Query\Permits\StockAcceptPermitted as StockAcceptPermittedQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;

/**
 * Stock accept permitted
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockAcceptPermitted extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitStock';

    /**
     * Handle query
     *
     * @param QueryInterface|StockAcceptPermittedQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stock = $this->getRepo()->fetchById($query->getId());
        $permitted = $stock->getStatus()->getId() == IrhpPermitStock::STATUS_SCORING_SUCCESSFUL;

        return ['result' => $permitted];
    }
}
