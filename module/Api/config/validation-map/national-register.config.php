<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

return [
    // Queries
    QueryHandler\Nr\ReputeUrl::class => Misc\IsInternalUser::class,
    // Commands
    CommandHandler\Cases\Si\ComplianceEpisode::class => Misc\NoValidationRequired::class //incoming xml from ATOS
];
