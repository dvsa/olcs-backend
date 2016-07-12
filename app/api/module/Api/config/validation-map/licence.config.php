<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

return [
    // Queries
    QueryHandler\Licence\Addresses::class => Misc\CanAccessLicenceWithId::class,
    QueryHandler\Licence\BusinessDetails::class => Misc\CanAccessLicenceWithId::class,
    QueryHandler\Licence\ConditionUndertaking::class => Misc\CanAccessLicenceWithId::class,
    QueryHandler\Licence\Licence::class => Misc\CanAccessLicenceWithId::class,
    QueryHandler\Licence\LicenceByNumber::class => Misc\CanAccessLicenceWithLicNo::class,
    QueryHandler\Licence\LicenceRegisteredAddress::class => Misc\NoValidationRequired::class,
    QueryHandler\Licence\OtherActiveLicences::class => Misc\CanAccessLicenceWithId::class,
    QueryHandler\Licence\TaxiPhv::class => Misc\CanAccessLicenceWithId::class,
    QueryHandler\Licence\TypeOfLicence::class => Misc\CanAccessLicenceWithId::class,

    // Commands
    CommandHandler\Licence\Curtail::class => Misc\IsInternalUser::class,
    CommandHandler\Licence\Suspend::class => Misc\IsInternalUser::class,
    CommandHandler\Licence\Surrender::class => Misc\IsInternalUser::class,
    CommandHandler\Licence\Revoke::class => Misc\IsInternalUser::class,
    CommandHandler\Licence\CreateVariation::class => Misc\CanAccessLicenceWithId::class,
    CommandHandler\Licence\UpdateAddresses::class => Misc\CanAccessLicenceWithId::class,
    CommandHandler\Licence\UpdateBusinessDetails::class => Misc\CanAccessLicenceWithId::class,
    CommandHandler\Licence\UpdateOperatingCentres::class => Misc\CanAccessLicenceWithId::class,
    CommandHandler\Licence\UpdateTypeOfLicence::class => Misc\CanAccessLicenceWithId::class,
    CommandHandler\Licence\EnqueueContinuationNotSought::class => Misc\IsSystemUser::class
];
