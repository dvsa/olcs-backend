<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    QueryHandler\IrhpPermitJurisdiction\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitJurisdiction\Update::class => IsInternalAdmin::class,
];
