<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * IRHP Stock
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class GetList extends AbstractListQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitStock';
    protected $bundle = [
        'irhpPermitType'=> ['name'],
        'irhpPermitRanges',
        'irhpPermitWindows',
        'country'
    ];
}
