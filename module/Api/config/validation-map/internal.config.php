<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanEditBusRegWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalOrSystemUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalEdit;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;

return [
    QueryHandler\Application\EnforcementArea::class => IsInternalUser::class,
    QueryHandler\Application\GetList::class => IsInternalUser::class,
    QueryHandler\Application\Interim::class => IsInternalUser::class,
    QueryHandler\Application\Overview::class => IsInternalUser::class,
    QueryHandler\Application\Publish::class => IsInternalUser::class,
    QueryHandler\Bus\BusRegDecision::class => IsInternalUser::class,
    QueryHandler\Bus\HistoryList::class => IsInternalUser::class,
    QueryHandler\Bus\ShortNoticeByBusReg::class => IsInternalUser::class,
    QueryHandler\ChangeOfEntity\ChangeOfEntity::class => IsInternalUser::class,
    QueryHandler\CompaniesHouse\AlertList::class => IsInternalUser::class,
    QueryHandler\CompaniesHouse\InsolvencyPractitioner::class => IsSystemUser::class,
    QueryHandler\Complaint\Complaint::class => IsInternalUser::class,
    QueryHandler\Complaint\ComplaintList::class => IsInternalUser::class,
    QueryHandler\ContinuationDetail\ChecklistReminders::class => IsInternalUser::class,
    QueryHandler\ContinuationDetail\GetList::class => IsInternalUser::class,
    QueryHandler\Cpms\ReportList::class => IsInternalUser::class,
    QueryHandler\DiscSequence\DiscPrefixes::class => IsInternalUser::class,
    QueryHandler\DiscSequence\DiscsNumbering::class => IsInternalUser::class,
    QueryHandler\Document\Document::class => IsInternalUser::class,
    QueryHandler\Document\DocumentList::class => IsInternalUser::class,
    QueryHandler\Document\Letter::class => IsInternalUser::class,
    QueryHandler\Document\TemplateParagraphs::class => IsInternalUser::class,
    QueryHandler\EnvironmentalComplaint\EnvironmentalComplaint::class => IsInternalUser::class,
    QueryHandler\EnvironmentalComplaint\EnvironmentalComplaintList::class => IsInternalUser::class,
    QueryHandler\Fee\FeeList::class => IsInternalUser::class,
    QueryHandler\Fee\FeeType::class => IsInternalUser::class,
    QueryHandler\Fee\FeeTypeList::class => IsInternalUser::class,
    QueryHandler\GracePeriod\GracePeriod::class => IsInternalUser::class,
    QueryHandler\GracePeriod\GracePeriods::class => IsInternalUser::class,
    QueryHandler\InspectionRequest\ApplicationInspectionRequestList::class => IsInternalUser::class,
    QueryHandler\InspectionRequest\InspectionRequest::class => IsInternalUser::class,
    QueryHandler\InspectionRequest\LicenceInspectionRequestList::class => IsInternalUser::class,
    QueryHandler\LicenceStatusRule\LicenceStatusRule::class => IsInternalUser::class,
    QueryHandler\Licence\ContinuationDetail::class => IsInternalUser::class,
    QueryHandler\Licence\EnforcementArea::class => IsInternalUser::class,
    QueryHandler\Licence\GetList::class => IsInternalUser::class,
    QueryHandler\Licence\LicenceDecisions::class => IsInternalUser::class,
    QueryHandler\Licence\Overview::class => IsInternalUser::class,
    QueryHandler\Opposition\Opposition::class => IsInternalUser::class,
    QueryHandler\Opposition\OppositionList::class => IsInternalUser::class,
    QueryHandler\Processing\History::class => IsInternalUser::class,
    QueryHandler\Processing\Note::class => IsInternalUser::class,
    QueryHandler\Processing\NoteList::class => IsInternalUser::class,
    QueryHandler\Publication\PendingList::class => IsInternalUser::class,
    QueryHandler\Publication\PublishedList::class => IsInternalUser::class,
    QueryHandler\Publication\PublicationLink::class => IsInternalUser::class,
    QueryHandler\Publication\PublicationLinkByTm::class => IsInternalUser::class,
    QueryHandler\Publication\PublicationLinkList::class => IsInternalUser::class,
    QueryHandler\Publication\Recipient::class => IsInternalUser::class,
    QueryHandler\Publication\RecipientList::class => IsInternalUser::class,
    QueryHandler\System\FinancialStandingRate::class => IsInternalUser::class,
    QueryHandler\System\FinancialStandingRateList::class => IsInternalUser::class,
    QueryHandler\Task\Task::class => IsInternalUser::class,
    QueryHandler\Task\TaskDetails::class => IsInternalUser::class,
    QueryHandler\Task\TaskList::class => IsInternalUser::class,
    QueryHandler\TmEmployment\GetList::class => IsInternalUser::class,
    QueryHandler\TmQualification\TmQualification::class => IsInternalUser::class,
    QueryHandler\TmQualification\TmQualificationsList::class => IsInternalUser::class,
    QueryHandler\TmResponsibilities\GetDocumentsForResponsibilities::class => IsInternalUser::class,
    QueryHandler\TmResponsibilities\TmResponsibilitiesList::class => IsInternalUser::class,
    QueryHandler\Tm\Documents::class => IsInternalUser::class,
    QueryHandler\User\User::class => IsInternalUser::class,
    QueryHandler\Variation\Variation::class => IsInternalUser::class,
    QueryHandler\Bus\PaginatedRegistrationHistoryList::class => IsInternalUser::class,
    CommandHandler\Application\Schedule41::class => IsInternalUser::class,
    CommandHandler\Application\Overview::class => IsInternalUser::class,
    CommandHandler\Application\PrintInterimDocument::class => IsInternalUser::class,
    CommandHandler\Application\Publish::class => IsInternalEdit::class,
    CommandHandler\Application\RefuseApplication::class => IsInternalEdit::class,
    CommandHandler\Application\RefuseInterim::class => IsInternalUser::class,
    CommandHandler\Application\ReviveApplication::class => IsInternalEdit::class,
    CommandHandler\Application\UpdateAuthSignature::class => IsInternalUser::class,
    CommandHandler\Application\UpdateInterim::class => IsInternalUser::class,
    CommandHandler\Bus\AdminCancelBusReg::class => IsInternalEdit::class,
    CommandHandler\Bus\CreateBus::class => IsInternalUser::class,
    CommandHandler\Bus\CreateNoticePeriod::class => IsInternalEdit::class,
    CommandHandler\Bus\CreateCancellation::class => IsInternalEdit::class,
    CommandHandler\Bus\CreateVariation::class => IsInternalEdit::class,
    CommandHandler\Bus\DeleteBus::class => IsInternalUser::class,
    CommandHandler\Bus\Ebsr\RequestMapQueue::class => IsInternalEdit::class,
    CommandHandler\Bus\GrantBusReg::class => IsInternalEdit::class,
    CommandHandler\Bus\RefuseBusReg::class => IsInternalEdit::class,
    CommandHandler\Bus\RefuseBusRegByShortNotice::class => IsInternalEdit::class,
    CommandHandler\Bus\ResetBusReg::class => IsInternalEdit::class,
    CommandHandler\Bus\UpdateQualitySchemes::class => IsInternalUser::class,
    CommandHandler\Bus\UpdateServiceDetails::class => IsInternalUser::class,
    CommandHandler\Bus\UpdateServiceRegister::class => IsInternalUser::class,
    CommandHandler\Bus\UpdateShortNotice::class => IsInternalUser::class,
    CommandHandler\Bus\UpdateStops::class => IsInternalUser::class,
    CommandHandler\Bus\UpdateTaAuthority::class => IsInternalUser::class,
    CommandHandler\Bus\WithdrawBusReg::class => IsInternalEdit::class,
    CommandHandler\Bus\PrintLetter::class => CanEditBusRegWithId::class,
    CommandHandler\ChangeOfEntity\CreateChangeOfEntity::class => IsInternalUser::class,
    CommandHandler\ChangeOfEntity\DeleteChangeOfEntity::class => IsInternalUser::class,
    CommandHandler\ChangeOfEntity\UpdateChangeOfEntity::class => IsInternalUser::class,
    CommandHandler\CompaniesHouse\CloseAlerts::class => IsInternalUser::class,
    CommandHandler\Complaint\CreateComplaint::class => IsInternalUser::class,
    CommandHandler\Complaint\DeleteComplaint::class => IsInternalUser::class,
    CommandHandler\Complaint\UpdateComplaint::class => IsInternalUser::class,
    CommandHandler\ConditionUndertaking\Delete::class => IsInternalUser::class,
    CommandHandler\ContinuationDetail\PrepareContinuations::class => IsInternalUser::class,
    CommandHandler\ContinuationDetail\Queue::class => IsInternalUser::class,
    CommandHandler\ContinuationDetail\Update::class => IsInternalOrSystemUser::class,
    CommandHandler\Continuation\Create::class => IsInternalUser::class,
    CommandHandler\Cpms\RequestReport::class => IsInternalUser::class,
    CommandHandler\EnvironmentalComplaint\CreateEnvironmentalComplaint::class => IsInternalUser::class,
    CommandHandler\EnvironmentalComplaint\DeleteEnvironmentalComplaint::class => IsInternalUser::class,
    CommandHandler\EnvironmentalComplaint\UpdateEnvironmentalComplaint::class => IsInternalUser::class,
    CommandHandler\Fee\ApproveWaive::class => IsInternalUser::class,
    CommandHandler\Fee\CreateFee::class => IsInternalUser::class,
    CommandHandler\Fee\RecommendWaive::class => IsInternalUser::class,
    CommandHandler\Fee\RefundFee::class => IsInternalOrSystemUser::class,
    CommandHandler\Fee\RejectWaive::class => IsInternalUser::class,
    CommandHandler\GoodsDisc\ConfirmPrinting::class => IsInternalUser::class,
    CommandHandler\GoodsDisc\PrintDiscs::class => IsInternalUser::class,
    CommandHandler\GracePeriod\CreateGracePeriod::class => IsInternalUser::class,
    CommandHandler\GracePeriod\DeleteGracePeriod::class => IsInternalUser::class,
    CommandHandler\GracePeriod\UpdateGracePeriod::class => IsInternalUser::class,
    CommandHandler\InspectionRequest\Create::class => IsInternalUser::class,
    CommandHandler\InspectionRequest\CreateFromGrant::class => IsInternalUser::class,
    CommandHandler\InspectionRequest\Delete::class => IsInternalUser::class,
    CommandHandler\InspectionRequest\Update::class => IsInternalUser::class,
    CommandHandler\LicenceStatusRule\CreateLicenceStatusRule::class => IsInternalUser::class,
    CommandHandler\LicenceStatusRule\DeleteLicenceStatusRule::class => IsInternalUser::class,
    CommandHandler\LicenceStatusRule\UpdateLicenceStatusRule::class => IsInternalUser::class,
    CommandHandler\Licence\ContinueLicence::class => IsInternalUser::class,
    CommandHandler\Licence\Overview::class => IsInternalUser::class,
    CommandHandler\Licence\UpdateTrafficArea::class => IsInternalUser::class,
    CommandHandler\Opposition\CreateOpposition::class => IsInternalUser::class,
    CommandHandler\Opposition\DeleteOpposition::class => IsInternalUser::class,
    CommandHandler\Opposition\UpdateOpposition::class => IsInternalUser::class,
    CommandHandler\OtherLicence\CreateForTm::class => IsInternalUser::class,
    CommandHandler\OtherLicence\CreateForTml::class => IsInternalUser::class,
    CommandHandler\Processing\Note\Create::class => IsInternalUser::class,
    CommandHandler\Processing\Note\Delete::class => IsInternalUser::class,
    CommandHandler\Processing\Note\Update::class => IsInternalUser::class,
    CommandHandler\Publication\Application::class => IsInternalUser::class,
    CommandHandler\Publication\Bus::class => IsInternalEdit::class,
    CommandHandler\Publication\CreateRecipient::class => IsInternalUser::class,
    CommandHandler\Publication\DeletePublicationLink::class => IsInternalUser::class,
    CommandHandler\Publication\DeleteRecipient::class => IsInternalUser::class,
    CommandHandler\Publication\Generate::class => IsInternalUser::class,
    CommandHandler\Publication\Publish::class => IsInternalUser::class,
    CommandHandler\Publication\UpdatePublicationLink::class => IsInternalUser::class,
    CommandHandler\Publication\UpdateRecipient::class => IsInternalUser::class,
    CommandHandler\Scan\CreateContinuationSeparatorSheet::class => IsInternalUser::class,
    CommandHandler\Scan\CreateSeparatorSheet::class => IsInternalUser::class,
    CommandHandler\Task\CloseTasks::class => IsInternalUser::class,
    CommandHandler\Task\CreateTask::class => IsInternalOrSystemUser::class,
    CommandHandler\Task\ReassignTasks::class => IsInternalUser::class,
    CommandHandler\Task\UpdateTask::class => IsInternalUser::class,
    CommandHandler\TmQualification\Create::class => IsInternalUser::class,
    CommandHandler\TmQualification\Delete::class => IsInternalUser::class,
    CommandHandler\TmQualification\Update::class => IsInternalUser::class,
    CommandHandler\Tm\Create::class => IsInternalUser::class,
    CommandHandler\Tm\Merge::class => IsInternalEdit::class,
    CommandHandler\Tm\Remove::class => IsInternalEdit::class,
    CommandHandler\Tm\Unmerge::class => IsInternalEdit::class,
    CommandHandler\Tm\Update::class => IsInternalUser::class,
    CommandHandler\User\CreateUser::class => IsInternalUser::class,
    CommandHandler\User\DeleteUser::class => IsInternalUser::class,
    CommandHandler\User\UpdateUser::class => IsInternalUser::class,
    CommandHandler\Variation\RestoreListConditionUndertaking::class => IsInternalUser::class,
    CommandHandler\Variation\UpdateInterim::class => IsInternalUser::class,
    CommandHandler\Tm\UndoDisqualification::class => IsInternalEdit::class,
];
