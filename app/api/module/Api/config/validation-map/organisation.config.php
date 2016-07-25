<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

return [
    // Queries
    QueryHandler\Organisation\Dashboard::class            => Misc\CanAccessOrganisationWithId::class,
    QueryHandler\Organisation\Organisation::class         => Misc\CanAccessOrganisationWithId::class,
    QueryHandler\Organisation\OutstandingFees::class      => Misc\CanAccessOrganisationWithId::class,

    // Commands
    CommandHandler\Organisation\UpdateBusinessType::class => Misc\CanAccessOrganisationWithId::class,
];
