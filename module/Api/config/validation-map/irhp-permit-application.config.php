<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithLicence;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    QueryHandler\IrhpPermitApplication\GetList::class => IsInternalUser::class,
    CommandHandler\IrhpPermitApplication\Create::class => CanAccessLicenceWithLicence::class,
];
