<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    CommandHandler\FeatureToggle\Create::class => IsInternalAdmin::class,
    CommandHandler\FeatureToggle\Update::class => IsInternalAdmin::class,
    CommandHandler\FeatureToggle\Delete::class => IsInternalAdmin::class,
    QueryHandler\FeatureToggle\ById::class => IsInternalAdmin::class,
    QueryHandler\FeatureToggle\GetList::class => IsInternalAdmin::class,
    QueryHandler\FeatureToggle\IsEnabled::class => NoValidationRequired::class,
];
