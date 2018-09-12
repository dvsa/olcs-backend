<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    QueryHandler\IrhpPermitWindow\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermitWindow\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Create::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Update::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitWindow\Delete::class => IsInternalAdmin::class,
];
