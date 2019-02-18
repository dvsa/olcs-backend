<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanSurrenderLicence;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Surrender\Delete as CanDeleteSurrender;

return [
    CommandHandler\Surrender\Create::class                              => CanSurrenderLicence::class,
    CommandHandler\Surrender\Update::class                              => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\Delete::class                              => CanDeleteSurrender::class,
    QueryHandler\Surrender\GetStatus::class                             => CanAccessLicenceWithId::class,
    QueryHandler\Surrender\ByLicence::class                             => CanAccessLicenceWithId::class,
    QueryHandler\Surrender\OpenBusReg::class                            => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\SubmitForm::class                          => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\Snapshot::class                            => CanAccessLicenceWithId::class,
    QueryHandler\Surrender\OpenCases::class                             => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\Approve::class                             => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\Withdraw::class                            => CanAccessLicenceWithId::class,
    QueryHandler\Surrender\PreviousLicenceStatus::class                 => CanAccessLicenceWithId::class,
];
