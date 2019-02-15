<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits\CanEditIrhpPermitApplicationWithId;

return [
    QueryHandler\IrhpPermitApplication\GetList::class => IsInternalUser::class,
    CommandHandler\IrhpPermitApplication\Delete::class => CanEditIrhpPermitApplicationWithId::class,
];
