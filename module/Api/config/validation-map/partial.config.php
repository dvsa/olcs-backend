<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

return [
    QueryHandler\Partial\ById::class => IsSystemAdmin::class,
    QueryHandler\Partial\GetList::class => IsSystemAdmin::class,
    CommandHandler\Partial\Update::class => IsSystemAdmin::class,
];
