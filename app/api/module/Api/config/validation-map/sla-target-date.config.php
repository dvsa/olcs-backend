<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;

return [
    // Queries
    QueryHandler\Sla\SlaTargetDate::class                           => Handler\Misc\IsInternalUser::class,

    // Commands
    CommandHandler\Sla\CreateSlaTargetDate::class                   => Handler\Misc\IsInternalUser::class,
    CommandHandler\Sla\UpdateSlaTargetDate::class                   => Handler\Misc\IsInternalUser::class,
];
