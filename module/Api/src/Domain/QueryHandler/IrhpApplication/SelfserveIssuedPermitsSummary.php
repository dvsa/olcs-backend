<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\SelfserveIssuedPermitsSummary as SelfserveIssuedPermitsSummmaryQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Selfserve issued permits summary
 */
final class SelfserveIssuedPermitsSummary extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

    /**
     * Handle query
     *
     * @param QueryInterface|SelfserveIssuedPermitsSummaryQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchSelfserveIssuedPermitsSummary(
            $query->getOrganisation()
        );
    }
}
