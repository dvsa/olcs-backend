<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NotIsAnonymousUser;

return [
    QueryHandler\IrhpPermit\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermit\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermit\Replace::class => IsInternalUser::class,
    CommandHandler\IrhpPermit\CreateReplacement::class => IsSideEffect::class,
    QueryHandler\IrhpPermit\ByPermitNumber::class => NotIsAnonymousUser::class,
    QueryHandler\IrhpPermitRange\ByPermitNumber::class => NotIsAnonymousUser::class,
];
