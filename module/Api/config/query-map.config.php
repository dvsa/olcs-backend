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

    // Licence
    TransferQuery\Licence\BusinessDetails::class => QueryHandler\Licence\BusinessDetails::class,
    TransferQuery\Licence\Licence::class => QueryHandler\Licence\Licence::class,
    TransferQuery\Licence\TypeOfLicence::class => QueryHandler\Licence\TypeOfLicence::class,
    TransferQuery\Licence\Safety::class => QueryHandler\Licence\Safety::class,
    TransferQuery\Licence\Addresses::class => QueryHandler\Licence\Addresses::class,
    TransferQuery\Licence\TransportManagers::class => QueryHandler\Licence\TransportManagers::class,

    // Other Licence
    TransferQuery\OtherLicence\OtherLicence::class => QueryHandler\OtherLicence\OtherLicence::class,

    // Organisation
    TransferQuery\Organisation\BusinessDetails::class => QueryHandler\Organisation\BusinessDetails::class,
    TransferQuery\Organisation\Organisation::class => QueryHandler\Organisation\Organisation::class,
    TransferQuery\Organisation\OutstandingFees::class => QueryHandler\Organisation\OutstandingFees::class,

    // Variation
    TransferQuery\Variation\Variation::class => QueryHandler\Variation\Variation::class,
    TransferQuery\Variation\TypeOfLicence::class => QueryHandler\Variation\TypeOfLicence::class,

    // Cases
    TransferQuery\Cases\Pi::class => QueryHandler\Cases\Pi::class,
    TransferQuery\Cases\LegacyOffence::class => QueryHandler\Cases\LegacyOffence::class,
    TransferQuery\Cases\LegacyOffenceList::class => QueryHandler\Cases\LegacyOffenceList::class,
    TransferQuery\Cases\ImpoundingList::class => QueryHandler\Cases\ImpoundingList::class,
    TransferQuery\Cases\Impounding::class => QueryHandler\Cases\Impounding::class,
    TransferQuery\Cases\Complaint\Complaint::class => QueryHandler\Cases\Complaint\Complaint::class,
    TransferQuery\Cases\Complaint\ComplaintList::class => QueryHandler\Cases\Complaint\ComplaintList::class,
    TransferQuery\Cases\EnvironmentalComplaint\EnvironmentalComplaint::class =>
        QueryHandler\Cases\EnvironmentalComplaint\EnvironmentalComplaint::class,
    TransferQuery\Cases\EnvironmentalComplaint\EnvironmentalComplaintList::class =>
        QueryHandler\Cases\EnvironmentalComplaint\EnvironmentalComplaintList::class,
    TransferQuery\Cases\ConditionUndertaking\ConditionUndertaking::class =>
        QueryHandler\Cases\ConditionUndertaking\ConditionUndertaking::class,
    TransferQuery\Cases\ConditionUndertaking\ConditionUndertakingList::class =>
        QueryHandler\Cases\ConditionUndertaking\ConditionUndertakingList::class,
    TransferQuery\Cases\EnvironmentalComplaint\EnvironmentalComplaint::class
        => QueryHandler\Cases\EnvironmentalComplaint\EnvironmentalComplaint::class,
    TransferQuery\Cases\EnvironmentalComplaint\EnvironmentalComplaintList::class
        => QueryHandler\Cases\EnvironmentalComplaint\EnvironmentalComplaintList::class,
    TransferQuery\Cases\Opposition\Opposition::class => QueryHandler\Cases\Opposition\Opposition::class,
    TransferQuery\Cases\Opposition\OppositionList::class =>
        QueryHandler\Cases\Opposition\OppositionList::class,

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

    // Transport Manager Application
    TransferQuery\TransportManagerApplication\GetDetails::class
        => QueryHandler\TransportManagerApplication\GetDetails::class,

    // TmEmployment
    TransferQuery\TmEmployment\GetSingle::class => QueryHandler\TmEmployment\GetSingle::class,
];
