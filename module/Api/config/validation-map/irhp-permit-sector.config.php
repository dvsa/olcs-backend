<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    QueryHandler\IrhpPermitSector\GetList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitSector\Update::class => IsInternalAdmin::class,
];
