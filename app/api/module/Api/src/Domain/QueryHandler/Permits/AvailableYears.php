<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableYears as AvailableYearsQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;

/**
 * Available years
 */
class AvailableYears extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpPermitWindow';

    /**
     * Handle query
     *
     * @param QueryInterface|AvailableYearsQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $permitType = $query->getType();

        if ($permitType != IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM) {
            // year selection is currently applicable only to ecmt short term
            return [
                'years' => []
            ];
        }

        $openWindows = $this->getRepo()->fetchOpenWindowsByType(
            $permitType,
            new DateTime()
        );

        $availableYears = [];
        foreach ($openWindows as $window) {
            $availableYears[] = $window->getIrhpPermitStock()->getValidTo(true)->format('Y');
        }

        $availableYears = array_unique($availableYears);
        sort($availableYears);

        return [
            'years' => $availableYears
        ];
    }
}
