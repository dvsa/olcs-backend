<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    CommandHandler\DataRetention\Populate::class => IsSystemUser::class,
    CommandHandler\DataRetention\DeleteEntities::class => IsSystemUser::class,
    CommandHandler\DataRetention\UpdateActionConfirmation::class => IsInternalUser::class,

    QueryHandler\DataRetention\GetRule::class => IsInternalUser::class,
    QueryHandler\DataRetention\RuleList::class => IsInternalUser::class,
    QueryHandler\DataRetention\Records::class => IsInternalUser::class,
    QueryHandler\DataRetention\GetProcessedList::class => IsInternalUser::class,
];
