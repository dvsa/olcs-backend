<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\AvailableTypes as AvailableTypesQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;

/**
 * Available types
 */
class AvailableTypes extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpPermitType';

    /**
     * Handle query
     *
     * @param QueryInterface|AvailableTypesQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        return ['types' => $this->getRepo()->fetchAvailableTypes(new DateTime())];
    }
}
