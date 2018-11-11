<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    QueryHandler\IrhpPermit\ById::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermit\GetList::class => IsInternalAdmin::class
];
