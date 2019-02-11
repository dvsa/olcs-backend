<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\MaxStockPermits as MaxStockPermitsQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get maximum permitted permits by stock id
 */
final class MaxStockPermits extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermit';

    protected $extraRepos = ['Licence'];

    /**
     * Handle query
     *
     * @param QueryInterface|MaxStockPermitsQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $licenceId = $query->getLicence();

        $livePermitCounts = $this->getRepo()->getLivePermitCountsGroupedByStock($licenceId);

        $licence = $this->getRepo('Licence')->fetchById($query->getLicence());
        $totAuthVehicles = $licence->getTotAuthVehicles();

        foreach ($livePermitCounts as $row) {
            $irhpPermitStockId = $row['irhpPermitStockId'];

            $maxPermits = $totAuthVehicles - $row['irhpPermitCount'];
            if ($maxPermits < 0) {
                $maxPermits = 0;
            }

            $response[$irhpPermitStockId] = $maxPermits;
        }

        return ['result' => $response];
    }
}
