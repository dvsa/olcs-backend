<?php

use Dvsa\Olcs\Transfer\Query as TransferQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Query\Bookmark as BookmarkQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark as BookmarkQueryHandler;

return [
    // Bookmarks
    BookmarkQuery\LicenceBundle::class => BookmarkQueryHandler\LicenceBundle::class,
    BookmarkQuery\TransportManagerBundle::class => BookmarkQueryHandler\TransportManagerBundle::class,
    BookmarkQuery\DocParagraphBundle::class => BookmarkQueryHandler\DocParagraphBundle::class,
    BookmarkQuery\OppositionBundle::class => BookmarkQueryHandler\OppositionBundle::class,
    BookmarkQuery\StatementBundle::class => BookmarkQueryHandler\StatementBundle::class,
    BookmarkQuery\CommunityLicBundle::class => BookmarkQueryHandler\CommunityLicBundle::class,
    BookmarkQuery\FeeBundle::class => BookmarkQueryHandler\FeeBundle::class,
    BookmarkQuery\ApplicationBundle::class => BookmarkQueryHandler\ApplicationBundle::class,
    BookmarkQuery\InterimUnlinkedTm::class => BookmarkQueryHandler\InterimUnlinkedTm::class,
    BookmarkQuery\InterimOperatingCentres::class => BookmarkQueryHandler\InterimOperatingCentres::class,
    BookmarkQuery\UserBundle::class => BookmarkQueryHandler\UserBundle::class,
    BookmarkQuery\BusRegBundle::class => BookmarkQueryHandler\BusRegBundle::class,
    BookmarkQuery\PublicationLinkBundle::class => BookmarkQueryHandler\PublicationLinkBundle::class,
    BookmarkQuery\PublicationBundle::class => BookmarkQueryHandler\PublicationBundle::class,
    BookmarkQuery\ConditionsUndertakings::class => BookmarkQueryHandler\ConditionsUndertakings::class,
    BookmarkQuery\GoodsDiscBundle::class => BookmarkQueryHandler\GoodsDiscBundle::class,
    BookmarkQuery\PsvDiscBundle::class => BookmarkQueryHandler\PsvDiscBundle::class,
    BookmarkQuery\InterimConditionsUndertakings::class
        => BookmarkQueryHandler\InterimConditionsUndertakings::class,
    BookmarkQuery\FstandingAdditionalVeh::class => BookmarkQueryHandler\FstandingAdditionalVeh::class,
    BookmarkQuery\PiHearingBundle::class => BookmarkQueryHandler\PiHearingBundle::class,
    BookmarkQuery\TotalContFee::class => BookmarkQueryHandler\TotalContFee::class,

    // Application
    TransferQuery\Application\Application::class => QueryHandler\Application\Application::class,
    TransferQuery\Application\FinancialHistory::class => QueryHandler\Application\FinancialHistory::class,
    TransferQuery\Application\FinancialEvidence::class => QueryHandler\Application\FinancialEvidence::class,
    TransferQuery\Application\PreviousConvictions::class => QueryHandler\Application\PreviousConvictions::class,
    TransferQuery\Application\Safety::class => QueryHandler\Application\Safety::class,
    TransferQuery\Application\Declaration::class => QueryHandler\Application\Declaration::class,
    TransferQuery\Application\LicenceHistory::class => QueryHandler\Application\LicenceHistory::class,
    TransferQuery\Application\TransportManagers::class => QueryHandler\Application\TransportManagers::class,
    TransferQuery\Application\GoodsVehicles::class => QueryHandler\Application\GoodsVehicles::class,
    TransferQuery\Application\VehicleDeclaration::class => QueryHandler\Application\VehicleDeclaration::class,
    TransferQuery\Application\Review::class => QueryHandler\Application\Review::class,
    TransferQuery\Application\Overview::class => QueryHandler\Application\Overview::class,
    TransferQuery\Application\EnforcementArea::class => QueryHandler\Application\EnforcementArea::class,
    TransferQuery\Application\Grant::class => QueryHandler\Application\Grant::class,
    TransferQuery\Application\People::class => QueryHandler\Application\People::class,
    TransferQuery\Application\OperatingCentre::class => QueryHandler\Application\OperatingCentre::class,
    TransferQuery\Application\TaxiPhv::class => QueryHandler\Application\TaxiPhv::class,

    // Licence
    TransferQuery\Licence\BusinessDetails::class => QueryHandler\Licence\BusinessDetails::class,
    TransferQuery\Licence\Licence::class => QueryHandler\Licence\Licence::class,
    TransferQuery\Licence\LicenceByNumber::class => QueryHandler\Licence\LicenceByNumber::class,
    TransferQuery\Licence\TypeOfLicence::class => QueryHandler\Licence\TypeOfLicence::class,
    TransferQuery\Licence\Safety::class => QueryHandler\Licence\Safety::class,
    TransferQuery\Licence\Addresses::class => QueryHandler\Licence\Addresses::class,
    TransferQuery\Licence\TransportManagers::class => QueryHandler\Licence\TransportManagers::class,
    TransferQuery\Licence\PsvDiscs::class => QueryHandler\Licence\PsvDiscs::class,
    TransferQuery\Licence\GoodsVehicles::class => QueryHandler\Licence\GoodsVehicles::class,
    TransferQuery\Licence\OtherActiveLicences::class => QueryHandler\Licence\OtherActiveLicences::class,
    TransferQuery\Licence\LicenceDecisions::class => QueryHandler\Licence\LicenceDecisions::class,
    TransferQuery\Licence\Overview::class => QueryHandler\Licence\Overview::class,
    TransferQuery\Licence\EnforcementArea::class => QueryHandler\Licence\EnforcementArea::class,
    TransferQuery\Licence\ConditionUndertaking::class => QueryHandler\Licence\ConditionUndertaking::class,
    TransferQuery\Licence\People::class => QueryHandler\Licence\People::class,
    TransferQuery\Licence\OperatingCentre::class => QueryHandler\Licence\OperatingCentre::class,
    TransferQuery\Licence\TaxiPhv::class => QueryHandler\Licence\TaxiPhv::class,

    // LicenceStatusRule
    TransferQuery\LicenceStatusRule\LicenceStatusRule::class => QueryHandler\LicenceStatusRule\LicenceStatusRule::class,

    // Other Licence
    TransferQuery\OtherLicence\OtherLicence::class => QueryHandler\OtherLicence\OtherLicence::class,

    // Organisation
    TransferQuery\Organisation\BusinessDetails::class => QueryHandler\Organisation\BusinessDetails::class,
    TransferQuery\Organisation\Organisation::class => QueryHandler\Organisation\Organisation::class,
    TransferQuery\Organisation\OutstandingFees::class => QueryHandler\Organisation\OutstandingFees::class,
    TransferQuery\Organisation\Dashboard::class => QueryHandler\Organisation\Dashboard::class,

    // Variation
    TransferQuery\Variation\Variation::class => QueryHandler\Variation\Variation::class,
    TransferQuery\Variation\TypeOfLicence::class => QueryHandler\Variation\TypeOfLicence::class,
    TransferQuery\Variation\GoodsVehicles::class => QueryHandler\Variation\GoodsVehicles::class,

    // Cases
    TransferQuery\Cases\Cases::class => QueryHandler\Cases\Cases::class,
    TransferQuery\Cases\CasesWithOppositionDates::class => QueryHandler\Cases\CasesWithOppositionDates::class,
    TransferQuery\Cases\Pi::class => QueryHandler\Cases\Pi::class,
    TransferQuery\Cases\AnnualTestHistory::class => QueryHandler\Cases\AnnualTestHistory::class,
    TransferQuery\Cases\LegacyOffence::class => QueryHandler\Cases\LegacyOffence::class,
    TransferQuery\Cases\LegacyOffenceList::class => QueryHandler\Cases\LegacyOffenceList::class,
    TransferQuery\Cases\Impounding\ImpoundingList::class => QueryHandler\Cases\Impounding\ImpoundingList::class,
    TransferQuery\Cases\Impounding\Impounding::class => QueryHandler\Cases\Impounding\Impounding::class,
    TransferQuery\Cases\ConditionUndertaking\ConditionUndertaking::class =>
        QueryHandler\Cases\ConditionUndertaking\ConditionUndertaking::class,
    TransferQuery\Cases\ConditionUndertaking\ConditionUndertakingList::class =>
        QueryHandler\Cases\ConditionUndertaking\ConditionUndertakingList::class,
    TransferQuery\Cases\ProposeToRevoke\ProposeToRevokeByCase::class
        => QueryHandler\Cases\ProposeToRevoke\ProposeToRevokeByCase::class,

    TransferQuery\Cases\Hearing\Appeal::class => QueryHandler\Cases\Hearing\Appeal::class,
    TransferQuery\Cases\Hearing\AppealByCase::class => QueryHandler\Cases\Hearing\Appeal::class,
    TransferQuery\Cases\Hearing\AppealList::class => QueryHandler\Cases\Hearing\AppealList::class,

    TransferQuery\Cases\Hearing\Stay::class => QueryHandler\Cases\Hearing\Stay::class,
    TransferQuery\Cases\Hearing\StayByCase::class => QueryHandler\Cases\Hearing\Stay::class,
    TransferQuery\Cases\Hearing\StayList::class => QueryHandler\Cases\Hearing\StayList::class,

    TransferQuery\Cases\Statement\Statement::class => QueryHandler\Cases\Statement\Statement::class,
    TransferQuery\Cases\Statement\StatementList::class => QueryHandler\Cases\Statement\StatementList::class,
    TransferQuery\Cases\ByTransportManager::class => QueryHandler\Cases\ByTransportManager::class,

    // Submission
    TransferQuery\Submission\SubmissionAction::class => QueryHandler\Submission\SubmissionAction::class,

    // Processing
    TransferQuery\Processing\History::class => QueryHandler\Processing\History::class,
    TransferQuery\Processing\Note::class => QueryHandler\Processing\Note::class,
    TransferQuery\Processing\NoteList::class => QueryHandler\Processing\NoteList::class,

    // Conviction - NOT Previous Conviction
    TransferQuery\Cases\Conviction\Conviction::class => QueryHandler\Cases\Conviction\Conviction::class,
    TransferQuery\Cases\Conviction\ConvictionList::class => QueryHandler\Cases\Conviction\ConvictionList::class,

    // NonPi
    TransferQuery\Cases\NonPi\Single::class => QueryHandler\Cases\NonPi\Single::class,
    TransferQuery\Cases\NonPi\Listing::class => QueryHandler\Cases\NonPi\Listing::class,

    // Prohibition
    TransferQuery\Cases\Prohibition\Prohibition::class => QueryHandler\Cases\Prohibition\Prohibition::class,
    TransferQuery\Cases\Prohibition\ProhibitionList::class
        => QueryHandler\Cases\Prohibition\ProhibitionList::class,

    // Prohibition / Defect
    TransferQuery\Cases\Prohibition\Defect::class => QueryHandler\Cases\Prohibition\Defect::class,
    TransferQuery\Cases\Prohibition\DefectList::class => QueryHandler\Cases\Prohibition\DefectList::class,

    // Previous Conviction
    TransferQuery\PreviousConviction\PreviousConviction::class
        => QueryHandler\PreviousConviction\PreviousConviction::class,

    // Company Subsidiary
    TransferQuery\CompanySubsidiary\CompanySubsidiary::class
        => QueryHandler\CompanySubsidiary\CompanySubsidiary::class,

    // Bus
    TransferQuery\Bus\BusReg::class => QueryHandler\Bus\Bus::class,
    TransferQuery\Bus\ShortNoticeByBusReg::class => QueryHandler\Bus\ShortNoticeByBusReg::class,
    TransferQuery\Bus\RegistrationHistoryList::class => QueryHandler\Bus\RegistrationHistoryList::class,
    TransferQuery\Bus\ByRouteNo::class => QueryHandler\Bus\ByRouteNo::class,

    // Trailer
    TransferQuery\Trailer\Trailers::class => QueryHandler\Trailer\Trailers::class,
    TransferQuery\Trailer\Trailers::class => QueryHandler\Trailer\Trailers::class,

    // Grace Periods
    TransferQuery\GracePeriod\GracePeriod::class => QueryHandler\GracePeriod\GracePeriod::class,
    TransferQuery\GracePeriod\GracePeriods::class => QueryHandler\GracePeriod\GracePeriods::class,

    // Irfo
    TransferQuery\Irfo\IrfoDetails::class => QueryHandler\Irfo\IrfoDetails::class,
    TransferQuery\Irfo\IrfoGvPermit::class => QueryHandler\Irfo\IrfoGvPermit::class,
    TransferQuery\Irfo\IrfoGvPermitList::class => QueryHandler\Irfo\IrfoGvPermitList::class,
    TransferQuery\Irfo\IrfoPermitStockList::class => QueryHandler\Irfo\IrfoPermitStockList::class,
    TransferQuery\Irfo\IrfoPsvAuth::class => QueryHandler\Irfo\IrfoPsvAuth::class,
    TransferQuery\Irfo\IrfoPsvAuthList::class => QueryHandler\Irfo\IrfoPsvAuthList::class,

    // Publication
    TransferQuery\Publication\Recipient::class => QueryHandler\Publication\Recipient::class,
    TransferQuery\Publication\RecipientList::class => QueryHandler\Publication\RecipientList::class,

    // My Account
    TransferQuery\MyAccount\MyAccount::class => QueryHandler\MyAccount\MyAccount::class,

    // User
    TransferQuery\User\Partner::class => QueryHandler\User\Partner::class,
    TransferQuery\User\PartnerList::class => QueryHandler\User\PartnerList::class,
    TransferQuery\User\User::class => QueryHandler\User\User::class,
    TransferQuery\User\UserList::class => QueryHandler\User\UserList::class,

    // Workshop
    TransferQuery\Workshop\Workshop::class => QueryHandler\Workshop\Workshop::class,

    // Correspondence
    TransferQuery\Correspondence\Correspondence::class => QueryHandler\Correspondence\Correspondence::class,
    TransferQuery\Correspondence\Correspondences::class => QueryHandler\Correspondence\Correspondences::class,

    // Payment
    TransferQuery\Payment\Payment::class => QueryHandler\Payment\Payment::class,
    TransferQuery\Payment\PaymentByReference::class => QueryHandler\Payment\PaymentByReference::class,

    // CommunityLic
    TransferQuery\CommunityLic\CommunityLic::class => QueryHandler\CommunityLic\CommunityLic::class,

    // Document
    TransferQuery\Document\TemplateParagraphs::class => QueryHandler\Document\TemplateParagraphs::class,
    TransferQuery\Document\Document::class => QueryHandler\Document\Document::class,
    TransferQuery\Document\DocumentList::class => QueryHandler\Document\DocumentList::class,

    // Transport Manager Application
    TransferQuery\TransportManagerApplication\GetDetails::class
        => QueryHandler\TransportManagerApplication\GetDetails::class,
    TransferQuery\TransportManagerApplication\GetList::class
        => QueryHandler\TransportManagerApplication\GetList::class,

    // TmEmployment
    TransferQuery\TmEmployment\GetSingle::class => QueryHandler\TmEmployment\GetSingle::class,

    // Bus Reg History View
    TransferQuery\Bus\HistoryList::class => QueryHandler\Bus\HistoryList::class,

    // Scan
    TransferQuery\Scan\GetSingle::class => QueryHandler\Scan\GetSingle::class,

    // Fee
    TransferQuery\Fee\Fee::class => QueryHandler\Fee\Fee::class,
    TransferQuery\Fee\FeeList::class => QueryHandler\Fee\FeeList::class,

    // Operator
    TransferQuery\Operator\BusinessDetails::class => QueryHandler\Operator\BusinessDetails::class,

    // Licence Vehicle
    TransferQuery\LicenceVehicle\LicenceVehicle::class => QueryHandler\LicenceVehicle\LicenceVehicle::class,

    // Inspection Request
    TransferQuery\InspectionRequest\OperatingCentres::class => QueryHandler\InspectionRequest\OperatingCentres::class,

    // Opposition
    TransferQuery\Opposition\Opposition::class => QueryHandler\Opposition\Opposition::class,
    TransferQuery\Opposition\OppositionList::class => QueryHandler\Opposition\OppositionList::class,

    // Complaint
    TransferQuery\Complaint\Complaint::class => QueryHandler\Complaint\Complaint::class,
    TransferQuery\Complaint\ComplaintList::class => QueryHandler\Complaint\ComplaintList::class,
    TransferQuery\EnvironmentalComplaint\EnvironmentalComplaint::class =>
        QueryHandler\EnvironmentalComplaint\EnvironmentalComplaint::class,
    TransferQuery\EnvironmentalComplaint\EnvironmentalComplaintList::class =>
        QueryHandler\EnvironmentalComplaint\EnvironmentalComplaintList::class,

    // Inspection Request
    TransferQuery\InspectionRequest\OperatingCentres::class => QueryHandler\InspectionRequest\OperatingCentres::class,
    TransferQuery\InspectionRequest\ApplicationInspectionRequestList::class =>
        QueryHandler\InspectionRequest\ApplicationInspectionRequestList::class,
    TransferQuery\InspectionRequest\LicenceInspectionRequestList::class =>
        QueryHandler\InspectionRequest\LicenceInspectionRequestList::class,
    TransferQuery\InspectionRequest\InspectionRequest::class => QueryHandler\InspectionRequest\InspectionRequest::class,

    // Change of Entity
    TransferQuery\ChangeOfEntity\ChangeOfEntity::class => QueryHandler\ChangeOfEntity\ChangeOfEntity::class,

    // ConditionUndertaking
    TransferQuery\ConditionUndertaking\GetList::class => QueryHandler\ConditionUndertaking\GetList::class,
    TransferQuery\ConditionUndertaking\Get::class => QueryHandler\ConditionUndertaking\Get::class,

    // Task
    TransferQuery\Task\TaskList::class => QueryHandler\Task\TaskList::class,
    TransferQuery\Task\Task::class => QueryHandler\Task\Task::class,
    TransferQuery\Task\TaskDetails::class => QueryHandler\Task\TaskDetails::class,
];
