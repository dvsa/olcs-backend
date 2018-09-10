<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    QueryHandler\IrhpPermitStock\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermitStock\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitStock\Create::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitStock\Update::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitStock\Delete::class => IsInternalAdmin::class,
];
