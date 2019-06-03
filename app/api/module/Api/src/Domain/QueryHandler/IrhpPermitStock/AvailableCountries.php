<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\Query\IrhpPermitStock\AvailableCountries as AvailableCountriesQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;

/**
 * Available countries
 */
class AvailableCountries extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'Country';

    /**
     * Handle query
     *
     * @param QueryInterface|AvailableCountriesQuery $query query
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleQuery(QueryInterface $query)
    {
        return [
            'countries' => $this->getRepo()->fetchAvailableCountriesForIrhpApplication(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                new DateTime()
            )
        ];
    }
}
