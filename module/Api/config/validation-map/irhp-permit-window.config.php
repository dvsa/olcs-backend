<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

return [
    QueryHandler\IrhpPermitWindow\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermitWindow\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Create::class => IsSystemAdmin::class,
    CommandHandler\IrhpPermitWindow\Update::class => IsSystemAdmin::class,
    CommandHandler\IrhpPermitWindow\Delete::class => IsSystemAdmin::class,
    CommandHandler\IrhpPermitWindow\Close::class => IsSideEffect::class,
];
