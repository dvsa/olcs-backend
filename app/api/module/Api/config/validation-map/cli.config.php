<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Queue as QueueCommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;
use Dvsa\Olcs\Cli\Domain\CommandHandler as CliCommandHandler;
use Dvsa\Olcs\Cli\Domain\QueryHandler as CliQueryHandler;

return [
    //  cli commands
    CliCommandHandler\CreateViExtractFiles::class => IsSystemUser::class,
    CliCommandHandler\SetViFlags::class => IsSystemUser::class,
    CliCommandHandler\DataGovUkExport::class => IsSystemUser::class,
    CliCommandHandler\DataDvaNiExport::class => IsSystemUser::class,
    CliCommandHandler\CompaniesHouseVsOlcsDiffsExport::class => IsSystemUser::class,
    CliCommandHandler\RemoveReadAudit::class => IsSystemUser::class,
    CliCommandHandler\CleanUpAbandonedVariations::class => IsSystemUser::class,
    CliCommandHandler\Bus\Expire::class => IsSystemUser::class,
    CliCommandHandler\ImportUsersFromCsv::class => IsSystemUser::class,
    CliCommandHandler\LastTmLetter::class => IsSystemUser::class,
    CliCommandHandler\Permits\MarkSuccessfulDaPermitApplications::class => IsSystemUser::class,
    CliCommandHandler\Permits\MarkSuccessfulRemainingPermitApplications::class => IsSystemUser::class,
    CliCommandHandler\Permits\MarkSuccessfulSectorPermitApplications::class => IsSystemUser::class,
    CliQueryHandler\Util\GetDbValue::class => IsSystemUser::class,
    CliQueryHandler\Permits\StockAvailability::class => IsSystemUser::class,
    CliQueryHandler\Permits\StockLackingRandomisedScore::class => IsSystemUser::class,

    //  api commands
    Dvsa\Olcs\Email\Domain\CommandHandler\ProcessInspectionRequestEmail::class => IsSystemUser::class,
    CommandHandler\Email\SendErruErrors::class => IsSystemUser::class,
    QueryHandler\Application\NotTakenUpList::class => IsSystemUser::class,
    CommandHandler\Vehicle\ProcessDuplicateVehicleWarnings::class => IsSystemUser::class,
    CommandHandler\Vehicle\ProcessDuplicateVehicleRemoval::class => IsSystemUser::class,
    CommandHandler\LicenceStatusRule\ProcessToRevokeCurtailSuspend::class => IsSystemUser::class,
    CommandHandler\LicenceStatusRule\ProcessToValid::class => IsSystemUser::class,
    CommandHandler\CompaniesHouse\EnqueueOrganisations::class => IsSystemUser::class,
    QueryHandler\Licence\ContinuationNotSoughtList::class => IsSystemUser::class,
    CommandHandler\Licence\CreateSurrenderPsvLicenceTasks::class => IsSystemUser::class,
    QueryHandler\Licence\PsvLicenceSurrenderList::class => IsSystemUser::class,
    CommandHandler\Licence\ProcessContinuationNotSought::class => IsSystemUser::class,
    CommandHandler\Email\SendContinuationNotSought::class => IsSystemUser::class,
    CommandHandler\Correspondence\ProcessInboxDocuments::class => IsSystemUser::class,
    CommandHandler\Transaction\ResolveOutstandingPayments::class => IsSystemUser::class,
    QueueCommandHandler\Complete::class => IsSideEffect::class,
    QueueCommandHandler\Failed::class => IsSideEffect::class,
    QueueCommandHandler\Retry::class => IsSideEffect::class,
    QueueCommandHandler\Create::class => IsSystemUser::class,
    QueueCommandHandler\Delete::class => IsSystemUser::class,
    QueryHandler\Queue\NextItem::class => IsSystemUser::class,
    CommandHandler\ContinuationDetail\ProcessReminder::class => IsSystemUser::class,
    CommandHandler\ContinuationDetail\Process::class => IsSystemUser::class,
    CommandHandler\CompaniesHouse\Compare::class => IsSystemUser::class,
    CommandHandler\CompaniesHouse\InitialLoad::class => IsSystemUser::class,
    CommandHandler\Licence\BatchVehicleListGeneratorForGoodsDiscs::class => IsSystemUser::class,
    CommandHandler\Discs\BatchVehicleListGeneratorForPsvDiscs::class => IsSystemUser::class,
    CommandHandler\Discs\PrintDiscs::class => IsSystemUser::class,
    CommandHandler\Bus\Ebsr\ProcessPack::class => IsSystemUser::class,
    CommandHandler\Bus\Ebsr\ProcessPackTransaction::class => IsSystemUser::class,
    CommandHandler\Bus\Ebsr\ProcessPackFailed::class => IsSystemUser::class,
    CommandHandler\Bus\Ebsr\ProcessRequestMap::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrWithdrawn::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrRefused::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrRefusedBySn::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrRegistered::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrCancelled::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrReceived::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrRefreshed::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrErrors::class => IsSystemUser::class,
    CommandHandler\Email\SendEbsrRequestMap::class => IsSystemUser::class,
    CommandHandler\Email\SendEcmtAppSubmitted::class => IsSystemUser::class,
    CommandHandler\Email\SendEcmtIssued::class => IsSystemUser::class,
    CommandHandler\Email\SendEcmtUnsuccessful::class => IsSystemUser::class,
    CommandHandler\Email\SendPublication::class => IsSystemUser::class,
    CommandHandler\Cases\Si\SendResponse::class => IsSystemUser::class,
    CommandHandler\PrintScheduler\PrintJob::class => IsSystemUser::class,
    CommandHandler\TransportManagerApplication\Snapshot::class => IsSystemUser::class,
];
