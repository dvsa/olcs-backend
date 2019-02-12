<?php
/**
 * Retrieve Irhp Permit list by Irhp ID
 *
 * @author Andy Newton <andy@vitri.ltd>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

class GetListByIrhpId extends AbstractListQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermit';
    protected $bundle = [
        'replaces',
        'irhpPermitRange' => [
            'irhpPermitStock' => ['country']
        ],
    ];
}
