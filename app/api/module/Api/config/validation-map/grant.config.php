<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessApplicationWithId;

return [
    CommandHandler\Application\Grant\ProcessApplicationOperatingCentres::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\Grant::class                                    => CanAccessApplicationWithId::class,
    CommandHandler\Application\GrantInterim::class                             => CanAccessApplicationWithId::class,
    CommandHandler\Variation\Grant::class                                      => CanAccessApplicationWithId::class,
    CommandHandler\Application\UndoGrant::class                                => IsInternalUser::class,
    QueryHandler\Application\Grant::class                                      => IsInternalUser::class,
];
