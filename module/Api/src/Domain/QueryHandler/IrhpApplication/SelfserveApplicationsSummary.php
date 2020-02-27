<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\SelfserveApplicationsSummary as SelfserveApplicationsSummmaryQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Selfserve applications summary
 */
final class SelfserveApplicationsSummary extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

    /**
     * Handle query
     *
     * @param QueryInterface|SelfserveApplicationsSummaryQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchSelfserveApplicationsSummary(
            $query->getOrganisation()
        );
    }
}
