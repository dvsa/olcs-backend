<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

return [
    // Queries
    QueryHandler\Licence\BusinessDetails::class => Misc\CanAccessLicenceWithId::class,

    // Commands
    CommandHandler\Licence\UpdateBusinessDetails::class => Misc\CanAccessLicenceWithId::class,

    CommandHandler\Licence\Curtail::class => Misc\IsInternalUser::class,
    CommandHandler\Licence\Suspend::class => Misc\IsInternalUser::class,
    CommandHandler\Licence\Surrender::class => Misc\IsInternalUser::class,
    CommandHandler\Licence\Revoke::class => Misc\IsInternalUser::class,
];
