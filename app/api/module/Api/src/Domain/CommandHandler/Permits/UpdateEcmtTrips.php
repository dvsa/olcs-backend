<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUpdateCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Update Ecmt trips information
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class UpdateEcmtTrips extends AbstractUpdateCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $commandMethodName = 'getEcmtTrips';
    protected $entityMethodName = 'updateTrips';
}
