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
    TransferCommand\Application\SubmitApplication::class => CommandHandler\Application\SubmitApplication::class,
    TransferCommand\Application\UpdateVehicles::class => CommandHandler\Application\UpdateVehicles::class,
    TransferCommand\Application\CreateGoodsVehicle::class => CommandHandler\Application\CreateGoodsVehicle::class,
    TransferCommand\Application\UpdateGoodsVehicle::class => CommandHandler\Application\UpdateGoodsVehicle::class,
    TransferCommand\Application\DeleteGoodsVehicle::class => CommandHandler\Application\DeleteGoodsVehicle::class,
    TransferCommand\Application\CreateVehicleListDocument::class
        => CommandHandler\Application\CreateVehicleListDocument::class,
    TransferCommand\Application\UpdateVehicleDeclaration::class =>
        CommandHandler\Application\UpdateVehicleDeclaration::class,
    TransferCommand\Application\WithdrawApplication::class
        => CommandHandler\Application\WithdrawApplication::class,
    TransferCommand\Application\ReviveApplication::class
        => CommandHandler\Application\ReviveApplication::class,
    TransferCommand\Application\RefuseApplication::class
        => CommandHandler\Application\RefuseApplication::class,
    TransferCommand\Application\NotTakenUpApplication::class
        => CommandHandler\Application\NotTakenUpApplication::class,
    TransferCommand\Application\Overview::class => CommandHandler\Application\Overview::class,
    TransferCommand\Application\CreateSnapshot::class => CommandHandler\Application\CreateSnapshot::class,
    TransferCommand\Application\Grant::class => CommandHandler\Application\Grant::class,
    TransferCommand\Application\UndoGrant::class => CommandHandler\Application\UndoGrant::class,
    Command\Application\GrantGoods::class => CommandHandler\Application\GrantGoods::class,
    Command\Application\GrantPsv::class => CommandHandler\Application\GrantPsv::class,
    Command\Application\CreateGrantFee::class => CommandHandler\Application\CreateGrantFee::class,
    Command\Application\Grant\CreateDiscRecords::class => CommandHandler\Application\Grant\CreateDiscRecords::class,
    Command\Application\Grant\CopyApplicationDataToLicence::class
        => CommandHandler\Application\Grant\CopyApplicationDataToLicence::class,
    Command\Application\Grant\ProcessApplicationOperatingCentres::class
        => CommandHandler\Application\Grant\ProcessApplicationOperatingCentres::class,
    Command\Application\Grant\CommonGrant::class => CommandHandler\Application\Grant\CommonGrant::class,
    Command\Application\Grant\GrantConditionUndertaking::class
        => CommandHandler\Application\Grant\GrantConditionUndertaking::class,
    Command\Application\Grant\GrantCommunityLicence::class
        => CommandHandler\Application\Grant\GrantCommunityLicence::class,
    Command\Application\Grant\GrantTransportManager::class
        => CommandHandler\Application\Grant\GrantTransportManager::class,
    Command\Application\Grant\GrantPeople::class => CommandHandler\Application\Grant\GrantPeople::class,
    Command\Application\Grant\ValidateApplication::class => CommandHandler\Application\Grant\ValidateApplication::class,
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
    TransferCommand\Bus\UpdateShortNotice::class => CommandHandler\Bus\UpdateShortNotice::class,
    TransferCommand\Bus\DeleteBus::class => CommandHandler\Bus\DeleteBus::class,

    // Transfer - Licence
    TransferCommand\Licence\UpdateTypeOfLicence::class => CommandHandler\Licence\UpdateTypeOfLicence::class,
    TransferCommand\Licence\UpdateAddresses::class => CommandHandler\Licence\UpdateAddresses::class,
    TransferCommand\Licence\UpdateBusinessDetails::class => CommandHandler\Licence\UpdateBusinessDetails::class,
    TransferCommand\Licence\UpdateCompanySubsidiary::class => CommandHandler\Licence\UpdateCompanySubsidiary::class,
    TransferCommand\Licence\CreateCompanySubsidiary::class => CommandHandler\Licence\CreateCompanySubsidiary::class,
    TransferCommand\Licence\DeleteCompanySubsidiary::class => CommandHandler\Licence\DeleteCompanySubsidiary::class,
    TransferCommand\Licence\UpdateSafety::class => CommandHandler\Licence\UpdateSafety::class,
    TransferCommand\Licence\CreateGoodsVehicle::class => CommandHandler\Licence\CreateGoodsVehicle::class,
    TransferCommand\Licence\CreateVehicleListDocument::class => CommandHandler\Licence\CreateVehicleListDocument::class,
    TransferCommand\Licence\TransferVehicles::class => CommandHandler\Licence\TransferVehicles::class,
    TransferCommand\Licence\PrintLicence::class => CommandHandler\Licence\PrintLicence::class,
    TransferCommand\Licence\CreatePeople::class => CommandHandler\Licence\CreatePeople::class,
    TransferCommand\Licence\UpdatePeople::class => CommandHandler\Licence\UpdatePeople::class,
    TransferCommand\Licence\DeletePeople::class => CommandHandler\Licence\DeletePeople::class,
    Command\Document\DispatchDocument::class => CommandHandler\Document\DispatchDocument::class,
    TransferCommand\Licence\Overview::class => CommandHandler\Licence\Overview::class,

    Command\Licence\Revoke::class => CommandHandler\Licence\Revoke::class,
    Command\Licence\Curtail::class => CommandHandler\Licence\Curtail::class,
    Command\Licence\Suspend::class => CommandHandler\Licence\Suspend::class,
    Command\Licence\Withdraw::class => CommandHandler\Licence\Withdraw::class,
    Command\Licence\Grant::class => CommandHandler\Licence\Grant::class,
    Command\Licence\Refuse::class => CommandHandler\Licence\Refuse::class,
    Command\Licence\NotTakenUp::class => CommandHandler\Licence\NotTakenUp::class,
    Command\Licence\UnderConsideration::class => CommandHandler\Licence\UnderConsideration::class,

    TransferCommand\Licence\CreatePsvDiscs::class => CommandHandler\Licence\CreatePsvDiscs::class,
    TransferCommand\Licence\VoidPsvDiscs::class => CommandHandler\Licence\VoidPsvDiscs::class,
    TransferCommand\Licence\ReplacePsvDiscs::class => CommandHandler\Licence\ReplacePsvDiscs::class,
    TransferCommand\Licence\CreateVariation::class => CommandHandler\Licence\CreateVariation::class,

    // Transfer - Variation
    TransferCommand\Variation\UpdateTypeOfLicence::class => CommandHandler\Variation\UpdateTypeOfLicence::class,
    TransferCommand\Variation\UpdateAddresses::class => CommandHandler\Variation\UpdateAddresses::class,
    TransferCommand\Variation\TransportManagerDeleteDelta::class
        => CommandHandler\Variation\TransportManagerDeleteDelta::class,
    TransferCommand\Variation\CreatePsvDiscs::class => CommandHandler\Variation\CreatePsvDiscs::class,
    TransferCommand\Variation\VoidPsvDiscs::class => CommandHandler\Variation\VoidPsvDiscs::class,
    TransferCommand\Variation\ReplacePsvDiscs::class => CommandHandler\Variation\ReplacePsvDiscs::class,
    TransferCommand\Variation\Grant::class => CommandHandler\Variation\Grant::class,

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
    TransferCommand\Irfo\UpdateIrfoDetails::class => CommandHandler\Irfo\UpdateIrfoDetails::class,
    TransferCommand\Irfo\CreateIrfoGvPermit::class => CommandHandler\Irfo\CreateIrfoGvPermit::class,
    TransferCommand\Irfo\UpdateIrfoGvPermit::class => CommandHandler\Irfo\UpdateIrfoGvPermit::class,
    TransferCommand\Irfo\CreateIrfoPermitStock::class => CommandHandler\Irfo\CreateIrfoPermitStock::class,
    TransferCommand\Irfo\CreateIrfoPsvAuth::class => CommandHandler\Irfo\CreateIrfoPsvAuth::class,
    TransferCommand\Irfo\UpdateIrfoPsvAuth::class => CommandHandler\Irfo\UpdateIrfoPsvAuth::class,

    // Transfer - Publication
    TransferCommand\Publication\CreateRecipient::class => CommandHandler\Publication\CreateRecipient::class,
    TransferCommand\Publication\UpdateRecipient::class => CommandHandler\Publication\UpdateRecipient::class,
    TransferCommand\Publication\DeleteRecipient::class => CommandHandler\Publication\DeleteRecipient::class,

    // Transfer - My Account
    TransferCommand\MyAccount\UpdateMyAccount::class => CommandHandler\MyAccount\UpdateMyAccount::class,

    // Transfer - User
    TransferCommand\User\CreatePartner::class => CommandHandler\User\CreatePartner::class,
    TransferCommand\User\UpdatePartner::class => CommandHandler\User\UpdatePartner::class,
    TransferCommand\User\DeletePartner::class => CommandHandler\User\DeletePartner::class,

    // Transfer - Cases
    TransferCommand\Cases\CreateCase::class => CommandHandler\Cases\CreateCase::class,
    TransferCommand\Cases\UpdateCase::class => CommandHandler\Cases\UpdateCase::class,
    TransferCommand\Cases\DeleteCase::class => CommandHandler\Cases\DeleteCase::class,

    //Transfer - Cases (note fields)
    TransferCommand\Cases\UpdateConvictionNote::class => CommandHandler\Cases\UpdateConvictionNote::class,
    TransferCommand\Cases\UpdateProhibitionNote::class => CommandHandler\Cases\UpdateProhibitionNote::class,

    // Transfer - Annual Test History
    TransferCommand\Cases\UpdateAnnualTestHistory::class => CommandHandler\Cases\UpdateAnnualTestHistory::class,

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
    TransferCommand\Complaint\CreateComplaint::class =>
        CommandHandler\Complaint\CreateComplaint::class,
    TransferCommand\Complaint\UpdateComplaint::class =>
        CommandHandler\Complaint\UpdateComplaint::class,
    TransferCommand\Complaint\DeleteComplaint::class =>
        CommandHandler\Complaint\DeleteComplaint::class,

    // Transfer - Environmental Complaint
    TransferCommand\EnvironmentalComplaint\CreateEnvironmentalComplaint::class =>
        CommandHandler\EnvironmentalComplaint\CreateEnvironmentalComplaint::class,
    TransferCommand\EnvironmentalComplaint\UpdateEnvironmentalComplaint::class =>
        CommandHandler\EnvironmentalComplaint\UpdateEnvironmentalComplaint::class,
    TransferCommand\EnvironmentalComplaint\DeleteEnvironmentalComplaint::class =>
        CommandHandler\EnvironmentalComplaint\DeleteEnvironmentalComplaint::class,

    // Transfer - Submission
    TransferCommand\Submission\CreateSubmissionAction::class =>
        CommandHandler\Submission\CreateSubmissionAction::class,
    TransferCommand\Submission\UpdateSubmissionAction::class =>
        CommandHandler\Submission\UpdateSubmissionAction::class,

    // Transfer - Document
    TransferCommand\Document\CreateDocument::class => CommandHandler\Document\CreateDocument::class,
    Command\Document\CreateDocumentSpecific::class => CommandHandler\Document\CreateDocumentSpecific::class,
    TransferCommand\Document\DeleteDocument::class => CommandHandler\Document\DeleteDocument::class,

    // Transfer - CommunityLic
    TransferCommand\CommunityLic\Application\Create::class => CommandHandler\CommunityLic\Application\Create::class,
    TransferCommand\CommunityLic\Application\CreateOfficeCopy::class =>
        CommandHandler\CommunityLic\Application\CreateOfficeCopy::class,
    TransferCommand\CommunityLic\Licence\Create::class => CommandHandler\CommunityLic\Licence\Create::class,
    TransferCommand\CommunityLic\Licence\CreateOfficeCopy::class =>
        CommandHandler\CommunityLic\Licence\CreateOfficeCopy::class,
    TransferCommand\CommunityLic\GenerateBatch::class => CommandHandler\CommunityLic\GenerateBatch::class,
    TransferCommand\CommunityLic\Void::class => CommandHandler\CommunityLic\Void::class,
    TransferCommand\CommunityLic\Restore::class => CommandHandler\CommunityLic\Restore::class,
    TransferCommand\CommunityLic\Stop::class => CommandHandler\CommunityLic\Stop::class,
    TransferCommand\CommunityLic\Reprint::class => CommandHandler\CommunityLic\Reprint::class,

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

    // Transfer - Appeal
    TransferCommand\Cases\Hearing\CreateAppeal::class =>
        CommandHandler\Cases\Hearing\CreateAppeal::class,
    TransferCommand\Cases\Hearing\UpdateAppeal::class =>
        CommandHandler\Cases\Hearing\UpdateAppeal::class,
    TransferCommand\Cases\Hearing\DeleteAppeal::class =>
        CommandHandler\Cases\Hearing\DeleteAppeal::class,

    // Transfer - Stay
    TransferCommand\Cases\Hearing\CreateStay::class =>
        CommandHandler\Cases\Hearing\CreateStay::class,
    TransferCommand\Cases\Hearing\UpdateStay::class =>
        CommandHandler\Cases\Hearing\UpdateStay::class,
    TransferCommand\Cases\Hearing\DeleteStay::class =>
        CommandHandler\Cases\Hearing\DeleteStay::class,

    // Transfer - Licence Status Rule
    TransferCommand\LicenceStatusRule\CreateLicenceStatusRule::class
        => CommandHandler\LicenceStatusRule\CreateLicenceStatusRule::class,
    TransferCommand\LicenceStatusRule\UpdateLicenceStatusRule::class
        => CommandHandler\LicenceStatusRule\UpdateLicenceStatusRule::class,
    TransferCommand\LicenceStatusRule\DeleteLicenceStatusRule::class
        => CommandHandler\LicenceStatusRule\DeleteLicenceStatusRule::class,

    // Transfer - Document
    TransferCommand\Document\CreateLetter::class => CommandHandler\Document\CreateLetter::class,

    // Transfer - Licence Decisions
    TransferCommand\Licence\RevokeLicence::class => CommandHandler\Licence\Revoke::class,
    TransferCommand\Licence\CurtailLicence::class => CommandHandler\Licence\Curtail::class,
    TransferCommand\Licence\SuspendLicence::class => CommandHandler\Licence\Suspend::class,
    TransferCommand\Licence\SurrenderLicence::class => CommandHandler\Licence\Surrender::class,
    TransferCommand\Licence\ResetToValid::class => CommandHandler\Licence\ResetToValid::class,

    // Transfer - ConditionUndertaking
    TransferCommand\Cases\ConditionUndertaking\CreateConditionUndertaking::class =>
        CommandHandler\Cases\ConditionUndertaking\CreateConditionUndertaking::class,
    TransferCommand\Cases\ConditionUndertaking\UpdateConditionUndertaking::class =>
        CommandHandler\Cases\ConditionUndertaking\UpdateConditionUndertaking::class,
    TransferCommand\Cases\ConditionUndertaking\DeleteConditionUndertaking::class =>
        CommandHandler\Cases\ConditionUndertaking\DeleteConditionUndertaking::class,

    // Transfer - Opposition
    TransferCommand\Opposition\CreateOpposition::class =>
        CommandHandler\Opposition\CreateOpposition::class,
    TransferCommand\Opposition\UpdateOpposition::class =>
        CommandHandler\Opposition\UpdateOpposition::class,
    TransferCommand\Opposition\DeleteOpposition::class =>
        CommandHandler\Opposition\DeleteOpposition::class,

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
    Command\Licence\UpdateTotalCommunityLicences::class => CommandHandler\Licence\UpdateTotalCommunityLicences::class,
    Command\Licence\SaveAddresses::class => CommandHandler\Licence\SaveAddresses::class,

    // Domain - Discs
    Command\Discs\CeaseGoodsDiscs::class => CommandHandler\Discs\CeaseGoodsDiscs::class,
    Command\Discs\CeasePsvDiscs::class => CommandHandler\Discs\CeasePsvDiscs::class,

    // Domain - Licence Vehicles
    Command\LicenceVehicle\RemoveLicenceVehicle::class => CommandHandler\LicenceVehicle\RemoveLicenceVehicle::class,

    // Domain - Transport Managers
    Command\Tm\DeleteTransportManagerLicence::class => CommandHandler\Tm\DeleteTransportManagerLicence::class,

    // Domain - ContactDetails
    Command\ContactDetails\SaveAddress::class => CommandHandler\ContactDetails\SaveAddress::class,

    // Domain - Task
    Command\Task\CreateTask::class => CommandHandler\Task\CreateTask::class,

    // Domain - Organisation
    Command\Organisation\UpdateTradingNames::class => CommandHandler\Organisation\UpdateTradingNames::class,

    // Domain - Fee
    Command\Fee\CreateFee::class => CommandHandler\Fee\CreateFee::class,
    Command\Fee\CancelFee::class => CommandHandler\Fee\CancelFee::class,
    Command\Fee\PayFee::class => CommandHandler\Fee\PayFee::class,
    TransferCommand\Fee\UpdateFee::class => CommandHandler\Fee\UpdateFee::class,
    TransferCommand\Fee\CreateMiscellaneousFee::class => CommandHandler\Fee\CreateMiscellaneousFee::class,

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

    // Domain - CommunityLic
    Command\CommunityLic\GenerateBatch::class => CommandHandler\CommunityLic\GenerateBatch::class,
    Command\CommunityLic\Application\CreateOfficeCopy::class =>
        CommandHandler\CommunityLic\Application\CreateOfficeCopy::class,
    Command\CommunityLic\Licence\CreateOfficeCopy::class =>
        CommandHandler\CommunityLic\Licence\CreateOfficeCopy::class,
    Command\CommunityLic\Void::class =>
        CommandHandler\CommunityLic\Void::class,

    // Domain - Document
    Command\Document\CreateDocument::class => CommandHandler\Document\CreateDocument::class,
    Command\Document\GenerateAndUploadDocument::class => CommandHandler\Document\GenerateAndUploadDocument::class,

    // Domain - PrintScheduler
    Command\PrintScheduler\EnqueueFile::class => CommandHandler\PrintScheduler\EnqueueFile::class,

    // Domain - LicenceStatusRule
    Command\LicenceStatusRule\ProcessToRevokeCurtailSuspend::class
        => CommandHandler\LicenceStatusRule\ProcessToRevokeCurtailSuspend::class,
    Command\LicenceStatusRule\ProcessToValid::class
        => CommandHandler\LicenceStatusRule\ProcessToValid::class,
    Command\LicenceStatusRule\RemoveLicenceStatusRulesForLicence::class
        => CommandHandler\LicenceStatusRule\RemoveLicenceStatusRulesForLicence::class,

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
    Command\Email\CreateCorrespondenceRecord::class => CommandHandler\Email\CreateCorrespondenceRecord::class,

    // Person
    TransferCommand\Person\Update::class => CommandHandler\Person\Update::class,

    // TM Employment
    TransferCommand\TmEmployment\DeleteList::class => CommandHandler\TmEmployment\DeleteList::class,
    TransferCommand\TmEmployment\Create::class => CommandHandler\TmEmployment\Create::class,
    TransferCommand\TmEmployment\Update::class => CommandHandler\TmEmployment\Update::class,

    // Transfer - Scan
    TransferCommand\Scan\CreateDocument::class => CommandHandler\Scan\CreateDocument::class,
    TransferCommand\Scan\CreateSeparatorSheet::class  => CommandHandler\Scan\CreateSeparatorSheet::class,
    TransferCommand\Scan\CreateContinuationSeparatorSheet::class  =>
        CommandHandler\Scan\CreateContinuationSeparatorSheet::class,

    // Domain - PrintScheduler
    Command\PrintScheduler\Enqueue::class  => CommandHandler\PrintScheduler\Enqueue::class,

    // Transfer - Operator
    TransferCommand\Operator\Create::class => CommandHandler\Operator\SaveOperator::class,
    TransferCommand\Operator\Update::class => CommandHandler\Operator\SaveOperator::class,

    // Vehicle
    Command\Vehicle\CreateGoodsVehicle::class => CommandHandler\Vehicle\CreateGoodsVehicle::class,
    TransferCommand\Vehicle\UpdateGoodsVehicle::class => CommandHandler\Vehicle\UpdateGoodsVehicle::class,
    TransferCommand\Vehicle\DeleteGoodsVehicle::class => CommandHandler\Vehicle\DeleteGoodsVehicle::class,
    Command\Vehicle\CeaseActiveDiscs::class => CommandHandler\Vehicle\CeaseActiveDiscs::class,
    TransferCommand\Vehicle\ReprintDisc::class => CommandHandler\Vehicle\ReprintDisc::class,
    Command\Vehicle\CreateGoodsDiscs::class => CommandHandler\Vehicle\CreateGoodsDiscs::class,

    // Transfer - InspectionRequest
    TransferCommand\InspectionRequest\Create::class => CommandHandler\InspectionRequest\Create::class,
    TransferCommand\InspectionRequest\Update::class => CommandHandler\InspectionRequest\Update::class,
    TransferCommand\InspectionRequest\CreateFromGrant::class => CommandHandler\InspectionRequest\CreateFromGrant::class,

    // Domain - InspectionRequest
    Command\InspectionRequest\SendInspectionRequest::class =>
        CommandHandler\InspectionRequest\SendInspectionRequest::class,
    Command\InspectionRequest\CreateFromGrant::class  => CommandHandler\InspectionRequest\CreateFromGrant::class,

    // Transfer - ChangeOfEntity
    TransferCommand\ChangeOfEntity\CreateChangeOfEntity::class =>
        CommandHandler\ChangeOfEntity\CreateChangeOfEntity::class,
    TransferCommand\ChangeOfEntity\UpdateChangeOfEntity::class =>
        CommandHandler\ChangeOfEntity\UpdateChangeOfEntity::class,
    TransferCommand\ChangeOfEntity\DeleteChangeOfEntity::class =>
        CommandHandler\ChangeOfEntity\DeleteChangeOfEntity::class,

    // OrganisationPerson
    TransferCommand\OrganisationPerson\PopulateFromCompaniesHouse::class =>
        CommandHandler\OrganisationPerson\PopulateFromCompaniesHouse::class,
];
