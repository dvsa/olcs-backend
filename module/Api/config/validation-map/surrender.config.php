<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanSurrenderLicence;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Surrender\Delete as CanDeleteSurrender;
use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessLicenceForSurrender;

return [
    CommandHandler\Surrender\Create::class                              => CanSurrenderLicence::class,
    CommandHandler\Surrender\Update::class                              => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\Delete::class                              => CanDeleteSurrender::class,
    QueryHandler\Surrender\GetStatus::class                             => CanAccessLicenceForSurrender::class,
    QueryHandler\Surrender\ByLicence::class                             => CanAccessLicenceForSurrender::class,
    QueryHandler\Surrender\OpenBusReg::class                            => IsInternalUser::class,
    CommandHandler\Surrender\SubmitForm::class                          => CanAccessLicenceForSurrender::class,
    CommandHandler\Surrender\Snapshot::class                            => CanAccessLicenceWithId::class,
    QueryHandler\Surrender\OpenCases::class                             => IsInternalUser::class
];
