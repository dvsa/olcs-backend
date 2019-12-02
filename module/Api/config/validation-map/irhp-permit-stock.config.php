<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

return [
    QueryHandler\IrhpPermitStock\ById::class => IsInternalUser::class,
    QueryHandler\IrhpPermitStock\GetList::class => IsInternalAdmin::class,
    QueryHandler\IrhpPermitStock\GetFormList::class => IsInternalAdmin::class,
    CommandHandler\IrhpPermitStock\Create::class => IsSystemAdmin::class,
    CommandHandler\IrhpPermitStock\Update::class => IsSystemAdmin::class,
    CommandHandler\IrhpPermitStock\Delete::class => IsSystemAdmin::class,
];
