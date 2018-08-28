<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    QueryHandler\Permits\SectorsList::class => NoValidationRequired::class,
    QueryHandler\Permits\ConstrainedCountries::class => NoValidationRequired::class,
    QueryHandler\Permits\EcmtCountriesList::class => NoValidationRequired::class,
    QueryHandler\Permits\EcmtPermits::class => NoValidationRequired::class,
    QueryHandler\Permits\EcmtPermitApplication::class => NoValidationRequired::class,
    QueryHandler\Permits\ById::class => NoValidationRequired::class,
    QueryHandler\Permits\EcmtPermitFees::class => NoValidationRequired::class,
    QueryHandler\Permits\EcmtApplicationByLicence::class => NoValidationRequired::class,
    CommandHandler\Permits\CreateEcmtPermits::class => NoValidationRequired::class,
    CommandHandler\Permits\CreateEcmtPermitApplication::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtEmissions::class => NoValidationRequired::class,
    CommandHandler\Permits\CancelEcmtPermitApplication::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateDeclaration::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtCabotage::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtPermitsRequired::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtCheckAnswers::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateDeclaration::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateInternationalJourney::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtTrips::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateSector::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtCountries::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtLicence::class => NoValidationRequired::class,
    CommandHandler\Permits\EcmtSubmitApplication::class => NoValidationRequired::class,
    CommandHandler\Permits\UpdateEcmtPermitApplication::class => NoValidationRequired::class,
    CommandHandler\Permits\WithdrawEcmtPermitApplication::class => NoValidationRequired::class,
    CommandHandler\Permits\CreateFullPermitApplication::class => NoValidationRequired::class,
    CommandHandler\Permits\WithdrawEcmtPermitApplication::class => NoValidationRequired::class,

];
