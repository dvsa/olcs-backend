<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessApplicationWithId;

return [
    CommandHandler\Application\Schedule41Approve::class => IsInternalUser::class,
    CommandHandler\Application\Schedule41Refuse::class  => IsInternalUser::class,
    CommandHandler\Application\Schedule41Cancel::class  => IsInternalUser::class,
    CommandHandler\Application\Schedule41Reset::class   => IsInternalUser::class,
    QueryHandler\Application\OutstandingFees::class     => CanAccessApplicationWithId::class,
];
