<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IRHP Permit Application
 *
 */
class GetList extends AbstractListQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $bundle = ['licence' => ['organisation']];
}
