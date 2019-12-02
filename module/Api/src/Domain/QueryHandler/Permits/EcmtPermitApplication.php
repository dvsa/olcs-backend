<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Get a list of ECMT Permit Applications
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class EcmtPermitApplication extends AbstractListQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'EcmtPermitApplication';

    protected $bundle = ['licence', 'irhpPermitApplications' => ['irhpPermitWindow' => ['irhpPermitStock']]];
}
