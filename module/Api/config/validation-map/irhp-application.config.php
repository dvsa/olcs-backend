<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits\CanEditPermitAppWithId;

return [
    CommandHandler\IrhpApplication\UpdateLicence::class => CanEditPermitAppWithId::class,
];
