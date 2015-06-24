<?php

use Dvsa\Olcs\Transfer\Command as TransferCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion as AppCompCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion as AppCompCommandHandler;

return [
    // Transfer - Application
    TransferCommand\Application\UpdateTypeOfLicence::class => CommandHandler\Application\UpdateTypeOfLicence::class,
    TransferCommand\Application\CreateApplication::class => CommandHandler\Application\CreateApplication::class,
    TransferCommand\Application\UpdateFinancialHistory::class =>
        CommandHandler\Application\UpdateFinancialHistory::class,
    TransferCommand\Application\UpdateFinancialEvidence::class =>
        CommandHandler\Application\UpdateFinancialEvidence::class,
    TransferCommand\Application\UpdateLicenceHistory::class => CommandHandler\Application\UpdateLicenceHistory::class,
    TransferCommand\Application\UpdatePreviousConvictions::class =>
        CommandHandler\Application\UpdatePreviousConvictions::class,
    TransferCommand\Application\UpdateDeclaration::class => CommandHandler\Application\UpdateDeclaration::class,
    TransferCommand\Application\UpdateBusinessDetails::class
        => CommandHandler\Application\UpdateBusinessDetails::class,
    TransferCommand\Application\UpdateCompanySubsidiary::class
        => CommandHandler\Application\UpdateCompanySubsidiary::class,
    TransferCommand\Application\CreateCompanySubsidiary::class
        => CommandHandler\Application\CreateCompanySubsidiary::class,
    TransferCommand\Application\DeleteCompanySubsidiary::class
        => CommandHandler\Application\DeleteCompanySubsidiary::class,
    TransferCommand\Application\UpdateAddresses::class
        => CommandHandler\Application\UpdateAddresses::class,
    TransferCommand\Application\UpdateSafety::class => CommandHandler\Application\UpdateSafety::class,
    TransferCommand\Application\DeleteWorkshop::class => CommandHandler\Application\DeleteWorkshop::class,
    TransferCommand\Application\CreateWorkshop::class => CommandHandler\Application\CreateWorkshop::class,
    TransferCommand\Application\UpdateWorkshop::class => CommandHandler\Application\UpdateWorkshop::class,

    // Transfer - Workshop
    TransferCommand\Workshop\DeleteWorkshop::class => CommandHandler\Workshop\DeleteWorkshop::class,
    TransferCommand\Workshop\CreateWorkshop::class => CommandHandler\Workshop\CreateWorkshop::class,
    TransferCommand\Workshop\UpdateWorkshop::class => CommandHandler\Workshop\UpdateWorkshop::class,

    // Transfer - Note
    TransferCommand\Processing\Note\Create::class => CommandHandler\Processing\Note\Create::class,
    TransferCommand\Processing\Note\Update::class => CommandHandler\Processing\Note\Update::class,
    TransferCommand\Processing\Note\Delete::class => CommandHandler\Processing\Note\Delete::class,

    // Non Pi
    TransferCommand\Cases\NonPi\Create::class => CommandHandler\Cases\NonPi\Create::class,
    TransferCommand\Cases\NonPi\Update::class => CommandHandler\Cases\NonPi\Update::class,
    TransferCommand\Cases\NonPi\Delete::class => CommandHandler\Cases\NonPi\Delete::class,

    // Transfer - Bus
    TransferCommand\Bus\UpdateStops::class => CommandHandler\Bus\UpdateStops::class,
    TransferCommand\Bus\UpdateQualitySchemes::class => CommandHandler\Bus\UpdateQualitySchemes::class,
    TransferCommand\Bus\UpdateTaAuthority::class => CommandHandler\Bus\UpdateTaAuthority::class,
    TransferCommand\Bus\UpdateServiceDetails::class => CommandHandler\Bus\UpdateServiceDetails::class,

    // Transfer - Licence
    TransferCommand\Licence\UpdateTypeOfLicence::class => CommandHandler\Licence\UpdateTypeOfLicence::class,
    TransferCommand\Licence\UpdateAddresses::class => CommandHandler\Licence\UpdateAddresses::class,
    TransferCommand\Licence\UpdateBusinessDetails::class => CommandHandler\Licence\UpdateBusinessDetails::class,
    TransferCommand\Licence\UpdateCompanySubsidiary::class => CommandHandler\Licence\UpdateCompanySubsidiary::class,
    TransferCommand\Licence\CreateCompanySubsidiary::class => CommandHandler\Licence\CreateCompanySubsidiary::class,
    TransferCommand\Licence\DeleteCompanySubsidiary::class => CommandHandler\Licence\DeleteCompanySubsidiary::class,
    TransferCommand\Licence\UpdateSafety::class => CommandHandler\Licence\UpdateSafety::class,

    Command\Licence\Revoke::class => CommandHandler\Licence\Revoke::class,
    Command\Licence\Curtail::class => CommandHandler\Licence\Curtail::class,
    Command\Licence\Suspend::class => CommandHandler\Licence\Suspend::class,

    TransferCommand\Licence\CreatePsvDiscs::class => CommandHandler\Licence\CreatePsvDiscs::class,
    TransferCommand\Licence\VoidPsvDiscs::class => CommandHandler\Licence\VoidPsvDiscs::class,
    TransferCommand\Licence\ReplacePsvDiscs::class => CommandHandler\Licence\ReplacePsvDiscs::class,

    // Transfer - Variation
    TransferCommand\Variation\UpdateTypeOfLicence::class => CommandHandler\Variation\UpdateTypeOfLicence::class,
    TransferCommand\Variation\UpdateAddresses::class => CommandHandler\Variation\UpdateAddresses::class,
    TransferCommand\Variation\TransportManagerDeleteDelta::class
        => CommandHandler\Variation\TransportManagerDeleteDelta::class,
    TransferCommand\Variation\CreatePsvDiscs::class => CommandHandler\Variation\CreatePsvDiscs::class,
    TransferCommand\Variation\VoidPsvDiscs::class => CommandHandler\Variation\VoidPsvDiscs::class,
    TransferCommand\Variation\ReplacePsvDiscs::class => CommandHandler\Variation\ReplacePsvDiscs::class,

    // Transfer - Organisation
    TransferCommand\Organisation\UpdateBusinessType::class => CommandHandler\Organisation\UpdateBusinessType::class,

    // Transfer - OtherLicence
    TransferCommand\OtherLicence\UpdateOtherLicence::class => CommandHandler\OtherLicence\UpdateOtherLicence::class,
    TransferCommand\OtherLicence\CreateOtherLicence::class => CommandHandler\OtherLicence\CreateOtherLicence::class,
    TransferCommand\OtherLicence\DeleteOtherLicence::class => CommandHandler\OtherLicence\DeleteOtherLicence::class,
    TransferCommand\OtherLicence\CreateForTma::class => CommandHandler\OtherLicence\CreateForTma::class,
    TransferCommand\OtherLicence\UpdateForTma::class => CommandHandler\OtherLicence\UpdateForTma::class,
    TransferCommand\OtherLicence\CreatePreviousLicence::class
        => CommandHandler\OtherLicence\CreatePreviousLicence::class,

    // Transfer - Previous Conviction
    TransferCommand\PreviousConviction\CreatePreviousConviction::class =>
        CommandHandler\PreviousConviction\CreatePreviousConviction::class,
    TransferCommand\PreviousConviction\UpdatePreviousConviction::class =>
        CommandHandler\PreviousConviction\UpdatePreviousConviction::class,
    TransferCommand\PreviousConviction\DeletePreviousConviction::class =>
        CommandHandler\PreviousConviction\DeletePreviousConviction::class,
    TransferCommand\PreviousConviction\CreateForTma::class
        => CommandHandler\PreviousConviction\CreateForTma::class,

    // Transfer - Trailer
    TransferCommand\Trailer\CreateTrailer::class => CommandHandler\Trailer\CreateTrailer::class,
    TransferCommand\Trailer\UpdateTrailer::class => CommandHandler\Trailer\UpdateTrailer::class,
    TransferCommand\Trailer\DeleteTrailer::class => CommandHandler\Trailer\DeleteTrailer::class,

    // Transfer - Grace Period
    TransferCommand\GracePeriod\CreateGracePeriod::class => CommandHandler\GracePeriod\CreateGracePeriod::class,
    TransferCommand\GracePeriod\UpdateGracePeriod::class => CommandHandler\GracePeriod\UpdateGracePeriod::class,
    TransferCommand\GracePeriod\DeleteGracePeriod::class => CommandHandler\GracePeriod\DeleteGracePeriod::class,

    // Transfer - Correspondence
    TransferCommand\Correspondence\AccessCorrespondence::class
        => CommandHandler\Correspondence\AccessCorrespondence::class,

    // Transfer - IRFO
    TransferCommand\Irfo\CreateIrfoGvPermit::class => CommandHandler\Irfo\CreateIrfoGvPermit::class,
    TransferCommand\Irfo\UpdateIrfoGvPermit::class => CommandHandler\Irfo\UpdateIrfoGvPermit::class,
    TransferCommand\Irfo\CreateIrfoPermitStock::class => CommandHandler\Irfo\CreateIrfoPermitStock::class,
    TransferCommand\Irfo\CreateIrfoPsvAuth::class => CommandHandler\Irfo\CreateIrfoPsvAuth::class,
    TransferCommand\Irfo\UpdateIrfoPsvAuth::class => CommandHandler\Irfo\UpdateIrfoPsvAuth::class,

    // Transfer - Publication
    TransferCommand\Publication\CreateRecipient::class => CommandHandler\Publication\CreateRecipient::class,
    TransferCommand\Publication\UpdateRecipient::class => CommandHandler\Publication\UpdateRecipient::class,
    TransferCommand\Publication\DeleteRecipient::class => CommandHandler\Publication\DeleteRecipient::class,

    // Transfer - User
    TransferCommand\User\CreatePartner::class => CommandHandler\User\CreatePartner::class,
    TransferCommand\User\UpdatePartner::class => CommandHandler\User\UpdatePartner::class,
    TransferCommand\User\DeletePartner::class => CommandHandler\User\DeletePartner::class,

    // Transfer - Impounding
    TransferCommand\Cases\Impounding\CreateImpounding::class =>
        CommandHandler\Cases\Impounding\CreateImpounding::class,
    TransferCommand\Cases\Impounding\UpdateImpounding::class =>
        CommandHandler\Cases\Impounding\UpdateImpounding::class,
    TransferCommand\Cases\Impounding\DeleteImpounding::class =>
        CommandHandler\Cases\Impounding\DeleteImpounding::class,

    // Transfer - ProposeToRevoke
    TransferCommand\Cases\ProposeToRevoke\CreateProposeToRevoke::class =>
        CommandHandler\Cases\ProposeToRevoke\CreateProposeToRevoke::class,
    TransferCommand\Cases\ProposeToRevoke\UpdateProposeToRevoke::class =>
        CommandHandler\Cases\ProposeToRevoke\UpdateProposeToRevoke::class,

    // Transfer - Complaint
    TransferCommand\Cases\Complaint\CreateComplaint::class =>
        CommandHandler\Cases\Complaint\CreateComplaint::class,
    TransferCommand\Cases\Complaint\UpdateComplaint::class =>
        CommandHandler\Cases\Complaint\UpdateComplaint::class,
    TransferCommand\Cases\Complaint\DeleteComplaint::class =>
        CommandHandler\Cases\Complaint\DeleteComplaint::class,

    // Transfer - Document
    TransferCommand\Document\CreateDocument::class => CommandHandler\Document\CreateDocument::class,
    TransferCommand\Document\DeleteDocument::class => CommandHandler\Document\DeleteDocument::class,

    // Conviction
    TransferCommand\Cases\Conviction\Create::class => CommandHandler\Cases\Conviction\Create::class,
    TransferCommand\Cases\Conviction\Update::class => CommandHandler\Cases\Conviction\Update::class,
    TransferCommand\Cases\Conviction\Delete::class => CommandHandler\Cases\Conviction\Delete::class,

    // Prohibition
    TransferCommand\Cases\Prohibition\Create::class => CommandHandler\Cases\Prohibition\Create::class,
    TransferCommand\Cases\Prohibition\Update::class => CommandHandler\Cases\Prohibition\Update::class,
    TransferCommand\Cases\Prohibition\Delete::class => CommandHandler\Cases\Prohibition\Delete::class,

    // Prohibition Defects
    TransferCommand\Cases\Prohibition\Defect\Create::class => CommandHandler\Cases\Prohibition\Defect\Create::class,
    TransferCommand\Cases\Prohibition\Defect\Update::class => CommandHandler\Cases\Prohibition\Defect\Update::class,
    TransferCommand\Cases\Prohibition\Defect\Delete::class => CommandHandler\Cases\Prohibition\Defect\Delete::class,

    // Transfer - Environmental Complaint
    TransferCommand\Cases\EnvironmentalComplaint\CreateEnvironmentalComplaint::class =>
        CommandHandler\Cases\EnvironmentalComplaint\CreateEnvironmentalComplaint::class,
    TransferCommand\Cases\EnvironmentalComplaint\UpdateEnvironmentalComplaint::class =>
        CommandHandler\Cases\EnvironmentalComplaint\UpdateEnvironmentalComplaint::class,
    TransferCommand\Cases\EnvironmentalComplaint\DeleteEnvironmentalComplaint::class =>
        CommandHandler\Cases\EnvironmentalComplaint\DeleteEnvironmentalComplaint::class,

    // Transfer - Document
    TransferCommand\Document\CreateLetter::class => CommandHandler\Document\CreateLetter::class,

    // Transfer - ConditionUndertaking
    TransferCommand\Cases\ConditionUndertaking\CreateConditionUndertaking::class =>
        CommandHandler\Cases\ConditionUndertaking\CreateConditionUndertaking::class,
    TransferCommand\Cases\ConditionUndertaking\UpdateConditionUndertaking::class =>
        CommandHandler\Cases\ConditionUndertaking\UpdateConditionUndertaking::class,
    TransferCommand\Cases\ConditionUndertaking\DeleteConditionUndertaking::class =>
        CommandHandler\Cases\ConditionUndertaking\DeleteConditionUndertaking::class,

    // Transfer - Opposition
    TransferCommand\Cases\Opposition\CreateOpposition::class =>
        CommandHandler\Cases\Opposition\CreateOpposition::class,
    TransferCommand\Cases\Opposition\UpdateOpposition::class =>
        CommandHandler\Cases\Opposition\UpdateOpposition::class,
    TransferCommand\Cases\Opposition\DeleteOpposition::class =>
        CommandHandler\Cases\Opposition\DeleteOpposition::class,

    // Transfer - Statement
    TransferCommand\Cases\Statement\CreateStatement::class =>
        CommandHandler\Cases\Statement\CreateStatement::class,
    TransferCommand\Cases\Statement\UpdateStatement::class =>
        CommandHandler\Cases\Statement\UpdateStatement::class,
    TransferCommand\Cases\Statement\DeleteStatement::class =>
        CommandHandler\Cases\Statement\DeleteStatement::class,

    // Domain - Application
    Command\Application\CreateApplicationFee::class => CommandHandler\Application\CreateApplicationFee::class,
    Command\Application\ResetApplication::class => CommandHandler\Application\ResetApplication::class,
    Command\Application\GenerateLicenceNumber::class => CommandHandler\Application\GenerateLicenceNumber::class,
    Command\Application\UpdateApplicationCompletion::class
        => CommandHandler\Application\UpdateApplicationCompletion::class,
    Command\Application\UpdateVariationCompletion::class => CommandHandler\Application\UpdateVariationCompletion::class,
    Command\Application\CreateFee::class => CommandHandler\Application\CreateFee::class,
    Command\Application\CancelAllInterimFees::class => CommandHandler\Application\CancelAllInterimFees::class,

    // Domain - Bus
    Command\Bus\CreateBusFee::class => CommandHandler\Bus\CreateBusFee::class,

    // Domain - Licence
    Command\Licence\CancelLicenceFees::class => CommandHandler\Licence\CancelLicenceFees::class,
    Command\Licence\SaveAddresses::class => CommandHandler\Licence\SaveAddresses::class,

    // Domain - ContactDetails
    Command\ContactDetails\SaveAddress::class => CommandHandler\ContactDetails\SaveAddress::class,

    // Domain - Task
    Command\Task\CreateTask::class => CommandHandler\Task\CreateTask::class,

    // Domain - Organisation
    Command\Organisation\UpdateTradingNames::class => CommandHandler\Organisation\UpdateTradingNames::class,

    // Domain - Fee
    Command\Fee\CreateFee::class => CommandHandler\Fee\CreateFee::class,
    Command\Fee\CancelFee::class => CommandHandler\Fee\CancelFee::class,

    // Domain - Payment
    Command\Payment\PayOutstandingFees::class => CommandHandler\Payment\PayOutstandingFees::class,
    TransferCommand\Payment\PayOutstandingFees::class => CommandHandler\Payment\PayOutstandingFees::class,
    TransferCommand\Payment\CompletePayment::class => CommandHandler\Payment\CompletePayment::class,
    Command\Payment\ResolvePayment::class => CommandHandler\Payment\ResolvePayment::class,

    // Domain - ApplicationCompletion
    AppCompCommand\UpdateTypeOfLicenceStatus::class => AppCompCommandHandler\UpdateTypeOfLicenceStatus::class,
    AppCompCommand\UpdateAddressesStatus::class => AppCompCommandHandler\UpdateAddressesStatus::class,
    AppCompCommand\UpdateBusinessTypeStatus::class => AppCompCommandHandler\UpdateBusinessTypeStatus::class,
    AppCompCommand\UpdateConvictionsPenaltiesStatus::class
        => AppCompCommandHandler\UpdateConvictionsPenaltiesStatus::class,
    AppCompCommand\UpdateFinancialEvidenceStatus::class => AppCompCommandHandler\UpdateFinancialEvidenceStatus::class,
    AppCompCommand\UpdateFinancialHistoryStatus::class => AppCompCommandHandler\UpdateFinancialHistoryStatus::class,
    AppCompCommand\UpdateLicenceHistoryStatus::class => AppCompCommandHandler\UpdateLicenceHistoryStatus::class,
    AppCompCommand\UpdateOperatingCentresStatus::class => AppCompCommandHandler\UpdateOperatingCentresStatus::class,
    AppCompCommand\UpdatePeopleStatus::class => AppCompCommandHandler\UpdatePeopleStatus::class,
    AppCompCommand\UpdateSafetyStatus::class => AppCompCommandHandler\UpdateSafetyStatus::class,
    AppCompCommand\UpdateVehiclesStatus::class => AppCompCommandHandler\UpdateVehiclesStatus::class,
    AppCompCommand\UpdateUndertakingsStatus::class => AppCompCommandHandler\UpdateUndertakingsStatus::class,
    AppCompCommand\UpdateConditionsUndertakingsStatus::class
        => AppCompCommandHandler\UpdateConditionsUndertakingsStatus::class,
    AppCompCommand\UpdateVehiclesDeclarationsStatus::class
        => AppCompCommandHandler\UpdateVehiclesDeclarationsStatus::class,
    AppCompCommand\UpdateVehiclesPsvStatus::class => AppCompCommandHandler\UpdateVehiclesPsvStatus::class,
    AppCompCommand\UpdateTransportManagersStatus::class => AppCompCommandHandler\UpdateTransportManagersStatus::class,
    AppCompCommand\UpdateTaxiPhvStatus::class => AppCompCommandHandler\UpdateTaxiPhvStatus::class,
    AppCompCommand\UpdateCommunityLicencesStatus::class => AppCompCommandHandler\UpdateCommunityLicencesStatus::class,
    AppCompCommand\UpdateBusinessDetailsStatus::class => AppCompCommandHandler\UpdateBusinessDetailsStatus::class,

    // Domain - LicenceStatusRule
    Command\LicenceStatusRule\ProcessToRevokeCurtailSuspend::class
        => CommandHandler\LicenceStatusRule\ProcessToRevokeCurtailSuspend::class,
    Command\LicenceStatusRule\ProcessToValid::class
        => CommandHandler\LicenceStatusRule\ProcessToValid::class,

    // Transport Manager Application
    TransferCommand\TransportManagerApplication\Delete::class
        => CommandHandler\TransportManagerApplication\Delete::class,
    TransferCommand\TransportManagerApplication\Create::class
        => CommandHandler\TransportManagerApplication\Create::class,
    TransferCommand\TransportManagerApplication\UpdateStatus::class
        => CommandHandler\TransportManagerApplication\UpdateStatus::class,
    TransferCommand\TransportManagerApplication\UpdateDetails::class
        => CommandHandler\TransportManagerApplication\UpdateDetails::class,

    // Email
    Command\Email\SendTmApplication::class => CommandHandler\Email\SendTmApplication::class,

    // Person
    TransferCommand\Person\Update::class => CommandHandler\Person\Update::class,

    // TM Employment
    TransferCommand\TmEmployment\DeleteList::class => CommandHandler\TmEmployment\DeleteList::class,
    TransferCommand\TmEmployment\Create::class => CommandHandler\TmEmployment\Create::class,
    TransferCommand\TmEmployment\Update::class => CommandHandler\TmEmployment\Update::class,

    // Transfer - Scan
    TransferCommand\Scan\CreateSeparatorSheet::class  => CommandHandler\Scan\CreateSeparatorSheet::class,
    TransferCommand\Scan\CreateContinuationSeparatorSheet::class  =>
        CommandHandler\Scan\CreateContinuationSeparatorSheet::class,

    // Domain - PrintScheduler
    Command\PrintScheduler\Enqueue::class  => CommandHandler\PrintScheduler\Enqueue::class,

    // Transfer - Operator
    TransferCommand\Operator\Create::class => CommandHandler\Operator\SaveOperator::class,
    TransferCommand\Operator\Update::class => CommandHandler\Operator\SaveOperator::class,
];
