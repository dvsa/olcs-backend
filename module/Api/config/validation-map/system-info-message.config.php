<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    CommandHandler\System\InfoMessage\Create::class => IsInternalAdmin::class,
    CommandHandler\System\InfoMessage\Update::class => IsInternalAdmin::class,
    CommandHandler\System\InfoMessage\Delete::class => IsInternalAdmin::class,
    QueryHandler\System\InfoMessage\Get::class => IsInternalAdmin::class,
    QueryHandler\System\InfoMessage\GetList::class => IsInternalAdmin::class,
];
