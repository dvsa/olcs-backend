<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Get all unpaid permits by application and status
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class UnpaidEcmtPermits extends AbstractListQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpCandidatePermit';
    protected $bundle = [
        'irhpPermitApplication' => [
            'ecmtPermitApplication' => [
                'fees'
            ]
        ],
        'irhpPermitRange' => [
            'countrys' => [
                'country'
            ],
            'irhpPermitStock'
        ],
        'status'
    ];
}
