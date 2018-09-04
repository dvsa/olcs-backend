<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    QueryHandler\IrhpPermitRange\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermitRange\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitRange\Create::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitRange\Update::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitRange\Delete::class => IsInternalAdmin::class,
];
