<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithLicence;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanDeleteSurrender;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanSurrenderLicence;

return [
    CommandHandler\Surrender\Create::class                              => CanSurrenderLicence::class,
    CommandHandler\Surrender\Update::class                              => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\Delete::class                              => CanAccessLicenceWithId::class,
    QueryHandler\Surrender\GetStatus::class                             => CanAccessLicenceWithId::class,
    QueryHandler\Surrender\ByLicence::class                             => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\SubmitForm::class                          => CanAccessLicenceWithId::class,
    CommandHandler\Surrender\Snapshot::class                            => CanAccessLicenceWithId::class,
];
