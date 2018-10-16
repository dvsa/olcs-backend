<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessOrganisationWithOrganisation;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithLicence;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NotIsAnonymousUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalEdit;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    QueryHandler\IrhpPermitStock\NextIrhpPermitStock::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\SectorsList::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\EcmtCountriesList::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\EcmtPermitApplication::class => CanAccessOrganisationWithOrganisation::class,
    QueryHandler\Permits\ById::class => Permits\CanAccessPermitAppWithId::class,
    QueryHandler\Permits\EcmtPermitFees::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\EcmtApplicationByLicence::class => CanAccessLicenceWithLicence::class,
    QueryHandler\Permits\ValidEcmtPermits::class => Permits\CanAccessPermitAppWithId::class,
    QueryHandler\Permits\UnpaidEcmtPermits::class => Permits\CanAccessPermitAppWithId::class,
    QueryHandler\IrhpPermitStock\NextIrhpPermitStock::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\OpenWindows::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\LastOpenWindow::class => NotIsAnonymousUser::class,
    CommandHandler\Permits\CreateEcmtPermitApplication::class => CanAccessLicenceWithLicence::class,
    CommandHandler\Permits\UpdateEcmtEmissions::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\CancelEcmtPermitApplication::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateDeclaration::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateEcmtCabotage::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateEcmtPermitsRequired::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateEcmtCheckAnswers::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateDeclaration::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateInternationalJourney::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateEcmtTrips::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateSector::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateEcmtCountries::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateEcmtLicence::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\EcmtSubmitApplication::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\UpdateEcmtPermitApplication::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\WithdrawEcmtPermitApplication::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\DeclineEcmtPermits::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\AcceptEcmtPermits::class => IsSideEffect::class,
    CommandHandler\Permits\CreateFullPermitApplication::class => IsInternalEdit::class,
    CommandHandler\Permits\UpdatePermitFee::class => IsSideEffect::class,
    CommandHandler\Permits\CompleteIssuePayment::class => Permits\CanEditPermitAppWithId::class,
    CommandHandler\Permits\TriggerProcessEcmtApplications::class => IsInternalAdmin::class,
    CommandHandler\Permits\GeneratePermit::class => IsInternalUser::class,
];
