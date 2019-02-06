<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;

/**
 * Delete IRHP Permit Application
 */
final class Delete extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'IrhpPermitApplication';
}
