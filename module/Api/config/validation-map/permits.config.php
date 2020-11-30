<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessOrganisationWithOrganisation;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithLicence;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NotIsAnonymousUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalEdit;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalOrSystemUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

return [
    QueryHandler\IrhpApplication\ById::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\QuestionAnswer::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\MaxStockPermits::class => CanAccessLicenceWithLicence::class,
    QueryHandler\IrhpApplication\MaxStockPermitsByApplication::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\FeeBreakdown::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\FeePerPermit::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\ApplicationStep::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\ApplicationPath::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\Documents::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\PermitsAvailable::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\PermitsAvailableByYear::class => NotIsAnonymousUser::class,
    QueryHandler\IrhpApplication\AnswersSummary::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\IrhpApplication\InternalApplicationsSummary::class => IsInternalUser::class,
    QueryHandler\IrhpApplication\BilateralMetadata::class => IsInternalUser::class,
    QueryHandler\IrhpApplication\SelfserveApplicationsSummary::class => CanAccessOrganisationWithOrganisation::class,
    QueryHandler\IrhpApplication\SelfserveIssuedPermitsSummary::class => CanAccessOrganisationWithOrganisation::class,
    QueryHandler\IrhpApplication\BilateralCountryAccessible::class => Permits\CanAccessIrhpApplicationWithId::class,
    QueryHandler\Permits\Sectors::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\EcmtPermitFees::class => NotIsAnonymousUser::class,
    QueryHandler\IrhpPermitStock\AvailableCountries::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\AvailableTypes::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\AvailableYears::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\AvailableStocks::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\MaxPermittedReached::class => CanAccessLicenceWithLicence::class,
    QueryHandler\Permits\EmissionsByYear::class => IsInternalUser::class,
    QueryHandler\Permits\OpenWindows::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\QueueRunScoringPermitted::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\CheckRunScoringPrerequisites::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\QueueAcceptScoringAndPostScoringReportPermitted::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\CheckAcceptScoringAndPostScoringReportPrerequisites::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\StockScoringPermitted::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\StockAcceptPermitted::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\StockOperationsPermitted::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\StockAlignmentReport::class => IsSystemAdmin::class,
    QueryHandler\Permits\PostScoringReport::class => NotIsAnonymousUser::class,
    QueryHandler\Permits\GetScoredPermitList::class => IsInternalAdmin::class,
    QueryHandler\Permits\ReadyToPrintType::class => IsInternalUser::class,
    QueryHandler\Permits\ReadyToPrintCountry::class => IsInternalUser::class,
    QueryHandler\Permits\ReadyToPrintStock::class => IsInternalUser::class,
    QueryHandler\Permits\ReadyToPrintRangeType::class => IsInternalUser::class,
    QueryHandler\Permits\ReadyToPrint::class => IsInternalUser::class,
    QueryHandler\Permits\ReadyToPrintConfirm::class => IsInternalUser::class,
    QueryHandler\Permits\DeviationData::class => IsInternalOrSystemUser::class,
    QueryHandler\IrhpPermitWindow\OpenByCountry::class => NotIsAnonymousUser::class,
    CommandHandler\IrhpApplication\UpdateCheckAnswers::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\Cancel::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\Terminate::class => IsInternalUser::class,
    CommandHandler\IrhpApplication\Withdraw::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\Grant::class => IsInternalUser::class,
    CommandHandler\IrhpApplication\ResetToNotYetSubmitted::class => IsInternalUser::class,
    CommandHandler\IrhpApplication\ReviveFromWithdrawn::class => IsInternalUser::class,
    CommandHandler\IrhpApplication\ReviveFromUnsuccessful::class => IsInternalUser::class,
    CommandHandler\IrhpApplication\UpdateCountries::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\UpdateMultipleNoOfPermits::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\SubmitApplicationStep::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\SubmitApplicationPath::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\Create::class => CanAccessLicenceWithLicence::class,
    CommandHandler\IrhpApplication\UpdateDeclaration::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\SubmitApplication::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\RegenerateApplicationFee::class => IsSideEffect::class,
    CommandHandler\IrhpApplication\RegenerateIssueFee::class => IsSideEffect::class,
    CommandHandler\IrhpApplication\UpdateCandidatePermitSelection::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\Permits\AcceptIrhpPermits::class => Permits\CanEditIrhpApplicationWithId::class,
    CommandHandler\Permits\GeneratePermitDocuments::class => IsSideEffect::class,
    CommandHandler\Permits\PrintPermits::class => IsInternalUser::class,
    CommandHandler\Permits\ProceedToStatus::class => IsSideEffect::class,

    CommandHandler\Permits\QueueRunScoring::class => IsSystemAdmin::class,
    CommandHandler\Permits\QueueAcceptScoring::class => IsSystemAdmin::class,
];
