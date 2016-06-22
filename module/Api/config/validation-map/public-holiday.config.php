<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    CommandHandler\System\PublicHoliday\Create::class => IsInternalAdmin::class,
    CommandHandler\System\PublicHoliday\Update::class => IsInternalAdmin::class,
    CommandHandler\System\PublicHoliday\Delete::class => IsInternalAdmin::class,
    QueryHandler\System\PublicHoliday\Get::class => IsInternalAdmin::class,
    QueryHandler\System\PublicHoliday\GetList::class => IsInternalAdmin::class,
];
