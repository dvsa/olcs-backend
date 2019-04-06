<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

return [
    QueryHandler\IrhpPermitRange\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermitRange\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitRange\Create::class => IsSystemAdmin::class,
    CommandHandler\IrhpPermitRange\Update::class => IsSystemAdmin::class,
    CommandHandler\IrhpPermitRange\Delete::class => IsSystemAdmin::class,
];
