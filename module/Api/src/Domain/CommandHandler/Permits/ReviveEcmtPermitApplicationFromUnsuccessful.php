<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\AbstractReviveFromUnsuccessful;

/**
 * Revive ECMT Permit Application from unsuccessful state
 */
final class ReviveEcmtPermitApplicationFromUnsuccessful extends AbstractReviveFromUnsuccessful
{
    protected $repoServiceName = 'EcmtPermitApplication';
}
