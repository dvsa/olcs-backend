<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    CommandHandler\Team\CreateTeam::class                                   => IsInternalUser::class,
    CommandHandler\Team\UpdateTeam::class                                   => IsInternalUser::class,
    CommandHandler\Team\DeleteTeam::class                                   => IsInternalUser::class,
    QueryHandler\Team\Team::class                                           => IsInternalUser::class,
    QueryHandler\Team\TeamList::class                                       => IsInternalUser::class,
];
