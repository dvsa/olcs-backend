<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get all valid ECMT permits by application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class ValidEcmtPermits extends AbstractListQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermit';
    protected $bundle = [
        'irhpPermitApplication' => [
            'ecmtPermitApplication'
        ],
        'irhpPermitRange' => [
            'countrys' => [
                'country'
            ],
            'irhpPermitStock'
        ],
        'status'
    ];

    public function handleQuery(QueryInterface $query)
    {
        $result = $this->getRepo()->fetchByEcmtApplicationPaginated($query);
        return [
            'results' => $this->resultList($result, $this->bundle),
            'count' => $this->getRepo()->fetchCount($query)
        ];
    }
}
