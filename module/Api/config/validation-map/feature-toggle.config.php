<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    CommandHandler\FeatureToggle\Create::class => IsInternalUser::class,
    CommandHandler\FeatureToggle\Update::class => IsInternalUser::class,
    CommandHandler\FeatureToggle\Delete::class => IsInternalUser::class,
    QueryHandler\FeatureToggle\ById::class => IsInternalUser::class,
    QueryHandler\FeatureToggle\GetList::class => IsInternalUser::class,
    QueryHandler\FeatureToggle\IsEnabled::class => NoValidationRequired::class,
];
