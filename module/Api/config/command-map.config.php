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

    // Transfer - Bus
    TransferCommand\Bus\UpdateStops::class => CommandHandler\Bus\UpdateStops::class,
    TransferCommand\Bus\UpdateQualitySchemes::class => CommandHandler\Bus\UpdateQualitySchemes::class,
    TransferCommand\Bus\UpdateTaAuthority::class => CommandHandler\Bus\UpdateTaAuthority::class,
    TransferCommand\Bus\UpdateServiceDetails::class => CommandHandler\Bus\UpdateServiceDetails::class,
    TransferCommand\Bus\UpdateShortNotice::class => CommandHandler\Bus\UpdateShortNotice::class,

    // Transfer - Licence
    TransferCommand\Licence\UpdateTypeOfLicence::class => CommandHandler\Licence\UpdateTypeOfLicence::class,
    TransferCommand\Licence\UpdateBusinessDetails::class => CommandHandler\Licence\UpdateBusinessDetails::class,
    TransferCommand\Licence\UpdateCompanySubsidiary::class => CommandHandler\Licence\UpdateCompanySubsidiary::class,
    TransferCommand\Licence\CreateCompanySubsidiary::class => CommandHandler\Licence\CreateCompanySubsidiary::class,
    TransferCommand\Licence\DeleteCompanySubsidiary::class => CommandHandler\Licence\DeleteCompanySubsidiary::class,
    TransferCommand\Licence\UpdateSafety::class => CommandHandler\Licence\UpdateSafety::class,

    // Transfer - Variation
    TransferCommand\Variation\UpdateTypeOfLicence::class => CommandHandler\Variation\UpdateTypeOfLicence::class,

    // Transfer - Organisation
    TransferCommand\Organisation\UpdateBusinessType::class => CommandHandler\Organisation\UpdateBusinessType::class,

    // Transfer - OtherLicence
    TransferCommand\OtherLicence\UpdateOtherLicence::class => CommandHandler\OtherLicence\UpdateOtherLicence::class,
    TransferCommand\OtherLicence\CreateOtherLicence::class => CommandHandler\OtherLicence\CreateOtherLicence::class,
    TransferCommand\OtherLicence\DeleteOtherLicence::class => CommandHandler\OtherLicence\DeleteOtherLicence::class,

    // Transfer - Previous Conviction
    TransferCommand\PreviousConviction\CreatePreviousConviction::class =>
        CommandHandler\PreviousConviction\CreatePreviousConviction::class,
    TransferCommand\PreviousConviction\UpdatePreviousConviction::class =>
        CommandHandler\PreviousConviction\UpdatePreviousConviction::class,
    TransferCommand\PreviousConviction\DeletePreviousConviction::class =>
        CommandHandler\PreviousConviction\DeletePreviousConviction::class,

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
    \Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoGvPermit::class
        => \Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\CreateIrfoGvPermit::class,
    \Dvsa\Olcs\Transfer\Command\Irfo\UpdateIrfoGvPermit::class
        => \Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\UpdateIrfoGvPermit::class,
    \Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoPermitStock::class
        => \Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\CreateIrfoPermitStock::class,
    \Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoPsvAuth::class
        => \Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\CreateIrfoPsvAuth::class,
    \Dvsa\Olcs\Transfer\Command\Irfo\UpdateIrfoPsvAuth::class
        => \Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\UpdateIrfoPsvAuth::class,

    // Transfer - Impounding
    TransferCommand\Cases\Impounding\CreateImpounding::class =>
        CommandHandler\Cases\Impounding\CreateImpounding::class,
    TransferCommand\Cases\Impounding\UpdateImpounding::class =>
        CommandHandler\Cases\Impounding\UpdateImpounding::class,
    TransferCommand\Cases\Impounding\DeleteImpounding::class =>
        CommandHandler\Cases\Impounding\DeleteImpounding::class,

    // Transfer - Complaint
    TransferCommand\Cases\Complaint\CreateComplaint::class =>
        CommandHandler\Cases\Complaint\CreateComplaint::class,
    TransferCommand\Cases\Complaint\UpdateComplaint::class =>
        CommandHandler\Cases\Complaint\UpdateComplaint::class,
    TransferCommand\Cases\Complaint\DeleteComplaint::class =>
        CommandHandler\Cases\Complaint\DeleteComplaint::class,

    // Conviction
    TransferCommand\Cases\Conviction\Create::class => CommandHandler\Cases\Conviction\Create::class,
    TransferCommand\Cases\Conviction\Update::class => CommandHandler\Cases\Conviction\Update::class,
    TransferCommand\Cases\Conviction\Delete::class => CommandHandler\Cases\Conviction\Delete::class,

    // Transfer - Environmental Complaint
    TransferCommand\Cases\EnvironmentalComplaint\CreateEnvironmentalComplaint::class =>
        CommandHandler\Cases\EnvironmentalComplaint\CreateEnvironmentalComplaint::class,
    TransferCommand\Cases\EnvironmentalComplaint\UpdateEnvironmentalComplaint::class =>
        CommandHandler\Cases\EnvironmentalComplaint\UpdateEnvironmentalComplaint::class,
    TransferCommand\Cases\EnvironmentalComplaint\DeleteEnvironmentalComplaint::class =>
        CommandHandler\Cases\EnvironmentalComplaint\DeleteEnvironmentalComplaint::class,

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

    // Domain - ContactDetails
    Command\ContactDetails\SaveAddress::class => CommandHandler\ContactDetails\SaveAddress::class,

    // Domain - Task
    Command\Task\CreateTask::class => CommandHandler\Task\CreateTask::class,

    // Domain - Organisation
    Command\Organisation\UpdateTradingNames::class => CommandHandler\Organisation\UpdateTradingNames::class,

    // Domain - Fee
    Command\Fee\CreateFee::class => CommandHandler\Fee\CreateFee::class,
    Command\Fee\CancelFee::class => CommandHandler\Fee\CancelFee::class,

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
];
