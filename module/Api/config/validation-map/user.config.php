<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;

return [
    // Queries
    QueryHandler\User\RoleList::class               => Handler\User\RoleList::class,

    // Commands
];
