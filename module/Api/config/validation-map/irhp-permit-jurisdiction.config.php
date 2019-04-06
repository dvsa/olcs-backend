<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

return [
    QueryHandler\IrhpPermitJurisdiction\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitJurisdiction\Update::class => IsSystemAdmin::class,
];
