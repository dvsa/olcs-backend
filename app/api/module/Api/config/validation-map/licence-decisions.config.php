<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    // Queries

    // Commands
    CommandHandler\Licence\Curtail::class                                               => IsInternalUser::class,
    CommandHandler\Licence\Suspend::class                                               => IsInternalUser::class,
    CommandHandler\Licence\Surrender::class                                             => IsInternalUser::class,
    CommandHandler\Licence\Revoke::class                                                => IsInternalUser::class,
];
