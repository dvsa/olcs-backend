<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;

return [
    QueryHandler\IrhpPermitWindow\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermitWindow\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Create::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Update::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Delete::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Close::class => IsSideEffect::class,
];
