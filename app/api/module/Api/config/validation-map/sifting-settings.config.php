<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    CommandHandler\EcmtPermits\UpdateSiftingSettings::class => IsInternalAdmin::class,
    QueryHandler\EcmtPermits\SiftingSettings::class => IsInternalAdmin::class,
];
