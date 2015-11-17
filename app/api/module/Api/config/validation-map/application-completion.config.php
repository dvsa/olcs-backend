<?php

use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion as AppCompHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessApplicationWithId;

return [
    AppCompHandler\UpdateAddressesStatus::class              => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateBusinessDetailsStatus::class        => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateBusinessTypeStatus::class           => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateCommunityLicencesStatus::class      => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateConditionsUndertakingsStatus::class => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateConvictionsPenaltiesStatus::class   => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateDeclarationsInternalStatus::class   => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateFinancialEvidenceStatus::class      => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateFinancialHistoryStatus::class       => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateLicenceHistoryStatus::class         => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateOperatingCentresStatus::class       => CanAccessApplicationWithId::class,
    AppCompHandler\UpdatePeopleStatus::class                 => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateSafetyStatus::class                 => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateTaxiPhvStatus::class                => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateTransportManagersStatus::class      => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateTypeOfLicenceStatus::class          => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateUndertakingsStatus::class           => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateVehiclesDeclarationsStatus::class   => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateVehiclesPsvStatus::class            => CanAccessApplicationWithId::class,
    AppCompHandler\UpdateVehiclesStatus::class               => CanAccessApplicationWithId::class,
];
