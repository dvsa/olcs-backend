<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion as AppCompCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Queue as QueueCommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;

/**
 * @NOTE This is the home of all commands that are only ever called as side-effects. The calling commands should
 * already have validation, and if the user has the ability to run the calling command, then all side-effects should be
 * runnable
 */
return [
    CommandHandler\Application\GrantGoods::class                                      => IsSideEffect::class,
    CommandHandler\Application\GrantPsv::class                                        => IsSideEffect::class,
    CommandHandler\Application\CreateGrantFee::class                                  => IsSideEffect::class,
    CommandHandler\Application\Grant\CreateDiscRecords::class                         => IsSideEffect::class,
    CommandHandler\Application\Grant\CopyApplicationDataToLicence::class              => IsSideEffect::class,
    CommandHandler\Application\Grant\ProcessApplicationOperatingCentres::class        => IsSideEffect::class,
    CommandHandler\Application\Grant\CommonGrant::class                               => IsSideEffect::class,
    CommandHandler\Application\Grant\GrantConditionUndertaking::class                 => IsSideEffect::class,
    CommandHandler\Application\Grant\GrantCommunityLicence::class                     => IsSideEffect::class,
    CommandHandler\Application\Grant\GrantTransportManager::class                     => IsSideEffect::class,
    CommandHandler\Application\Grant\GrantPeople::class                               => IsSideEffect::class,
    CommandHandler\Application\Grant\ValidateApplication::class                       => IsSideEffect::class,
    CommandHandler\Application\Grant\Schedule41::class                                => IsSideEffect::class,
    CommandHandler\Application\Grant\ProcessDuplicateVehicles::class                  => IsSideEffect::class,
    CommandHandler\Application\InForceInterim::class                                  => IsSideEffect::class,
    CommandHandler\Application\EndInterim::class                                      => IsSideEffect::class,
    CommandHandler\Application\HandleOcVariationFees::class                           => IsSideEffect::class,
    CommandHandler\Application\CreateTexTask::class                                   => IsSideEffect::class,
    CommandHandler\Application\CloseTexTask::class                                    => IsSideEffect::class,
    CommandHandler\Application\CloseFeeDueTask::class                                 => IsSideEffect::class,
    CommandHandler\Task\CreateTranslateToWelshTask::class                             => IsSideEffect::class,
    CommandHandler\Bus\Ebsr\CreateSubmission::class                                   => IsSideEffect::class,
    CommandHandler\Bus\Ebsr\DeleteSubmission::class                                   => IsSideEffect::class,
    CommandHandler\Document\DispatchDocument::class                                   => IsSideEffect::class,
    CommandHandler\Licence\VoidAllCommunityLicences::class                            => IsSideEffect::class,
    CommandHandler\Licence\ReturnAllCommunityLicences::class                          => IsSideEffect::class,
    CommandHandler\Licence\ExpireAllCommunityLicences::class                          => IsSideEffect::class,
    CommandHandler\Licence\TmNominatedTask::class                                     => IsSideEffect::class,
    CommandHandler\Licence\Withdraw::class                                            => IsSideEffect::class,
    CommandHandler\Licence\Grant::class                                               => IsSideEffect::class,
    CommandHandler\Licence\Refuse::class                                              => IsSideEffect::class,
    CommandHandler\Licence\NotTakenUp::class                                          => IsSideEffect::class,
    CommandHandler\Licence\UnderConsideration::class                                  => IsSideEffect::class,
    CommandHandler\Organisation\ChangeBusinessType::class                             => IsSideEffect::class,
    CommandHandler\Document\CreateDocumentSpecific::class                             => IsSideEffect::class,
    CommandHandler\Application\CreateApplicationFee::class                            => IsSideEffect::class,
    CommandHandler\Application\ResetApplication::class                                => IsSideEffect::class,
    CommandHandler\Application\GenerateLicenceNumber::class                           => IsSideEffect::class,
    CommandHandler\Application\UpdateVariationCompletion::class                       => IsSideEffect::class,
    CommandHandler\Application\CreateFee::class                                       => IsSideEffect::class,
    CommandHandler\Application\CancelAllInterimFees::class                            => IsSideEffect::class,
    CommandHandler\Application\CancelOutstandingFees::class                           => IsSideEffect::class,
    CommandHandler\Application\SetDefaultTrafficAreaAndEnforcementArea::class         => IsSideEffect::class,
    CommandHandler\Application\DeleteApplication::class                               => IsSideEffect::class,
    CommandHandler\ApplicationOperatingCentre\CreateApplicationOperatingCentre::class => IsSideEffect::class,
    CommandHandler\ApplicationOperatingCentre\DeleteApplicationOperatingCentre::class => IsSideEffect::class,
    CommandHandler\LicenceOperatingCentre\AssociateS4::class                          => IsSideEffect::class,
    CommandHandler\LicenceOperatingCentre\DisassociateS4::class                       => IsSideEffect::class,
    CommandHandler\OperatingCentre\DeleteApplicationLinks::class                      => IsSideEffect::class,
    CommandHandler\OperatingCentre\DeleteConditionUndertakings::class                 => IsSideEffect::class,
    CommandHandler\OperatingCentre\DeleteTmLinks::class                               => IsSideEffect::class,
    CommandHandler\Cases\ConditionUndertaking\CreateConditionUndertaking::class       => IsSideEffect::class,
    CommandHandler\Cases\ConditionUndertaking\DeleteConditionUndertakingS4::class     => IsSideEffect::class,
    CommandHandler\Cases\Si\ComplianceEpisode::class                                  => IsSideEffect::class,
    CommandHandler\Schedule41\CreateS4::class                                         => IsSideEffect::class,
    CommandHandler\Schedule41\ApproveS4::class                                        => IsSideEffect::class,
    CommandHandler\Schedule41\ResetS4::class                                          => IsSideEffect::class,
    CommandHandler\Schedule41\RefuseS4::class                                         => IsSideEffect::class,
    CommandHandler\Schedule41\CancelS4::class                                         => IsSideEffect::class,
    CommandHandler\Bus\CreateBusFee::class                                            => IsSideEffect::class,
    CommandHandler\Licence\CancelLicenceFees::class                                   => IsSideEffect::class,
    CommandHandler\Licence\UpdateTotalCommunityLicences::class                        => IsSideEffect::class,
    CommandHandler\Licence\SaveAddresses::class                                       => IsSideEffect::class,
    CommandHandler\Licence\SaveBusinessDetails::class                                 => IsSideEffect::class,
    CommandHandler\Publication\PiHearing::class                                       => IsSideEffect::class,
    CommandHandler\Publication\CreateNextPublication::class                           => IsSideEffect::class,
    CommandHandler\Publication\Licence::class                                         => IsSideEffect::class,
    CommandHandler\Publication\Impounding::class                                      => IsSideEffect::class,
    CommandHandler\Publication\CreatePoliceDocument::class                            => IsSideEffect::class,
    CommandHandler\Discs\CeaseGoodsDiscs::class                                       => IsSideEffect::class,
    CommandHandler\Discs\CeaseGoodsDiscsForApplication::class                         => IsSideEffect::class,
    CommandHandler\Discs\CeasePsvDiscs::class                                         => IsSideEffect::class,
    CommandHandler\LicenceVehicle\RemoveLicenceVehicle::class                         => IsSideEffect::class,
    CommandHandler\Vehicle\ProcessDuplicateVehicleWarning::class                      => IsSideEffect::class,
    CommandHandler\Tm\DeleteTransportManagerLicence::class                            => IsSideEffect::class,
    CommandHandler\ContactDetails\SaveAddress::class                                  => IsSideEffect::class,
    CommandHandler\Organisation\UpdateTradingNames::class                             => IsSideEffect::class,
    CommandHandler\Fee\CancelFee::class                                               => IsSideEffect::class,
    CommandHandler\Fee\CancelIrfoGvPermitFees::class                                  => IsSideEffect::class,
    CommandHandler\Fee\CancelIrfoPsvAuthFees::class                                   => IsSideEffect::class,
    CommandHandler\Fee\PayFee::class                                                  => IsSideEffect::class,
    CommandHandler\Transaction\ResolvePayment::class                                  => IsSideEffect::class,
    AppCompCommandHandler\UpdateTypeOfLicenceStatus::class                            => IsSideEffect::class,
    AppCompCommandHandler\UpdateAddressesStatus::class                                => IsSideEffect::class,
    AppCompCommandHandler\UpdateBusinessTypeStatus::class                             => IsSideEffect::class,
    AppCompCommandHandler\UpdateConvictionsPenaltiesStatus::class                     => IsSideEffect::class,
    AppCompCommandHandler\UpdateFinancialEvidenceStatus::class                        => IsSideEffect::class,
    AppCompCommandHandler\UpdateFinancialHistoryStatus::class                         => IsSideEffect::class,
    AppCompCommandHandler\UpdateLicenceHistoryStatus::class                           => IsSideEffect::class,
    AppCompCommandHandler\UpdateOperatingCentresStatus::class                         => IsSideEffect::class,
    AppCompCommandHandler\UpdatePeopleStatus::class                                   => IsSideEffect::class,
    AppCompCommandHandler\UpdateSafetyStatus::class                                   => IsSideEffect::class,
    AppCompCommandHandler\UpdateVehiclesStatus::class                                 => IsSideEffect::class,
    AppCompCommandHandler\UpdateUndertakingsStatus::class                             => IsSideEffect::class,
    AppCompCommandHandler\UpdateConditionsUndertakingsStatus::class                   => IsSideEffect::class,
    AppCompCommandHandler\UpdateVehiclesDeclarationsStatus::class                     => IsSideEffect::class,
    AppCompCommandHandler\UpdateVehiclesPsvStatus::class                              => IsSideEffect::class,
    AppCompCommandHandler\UpdateTransportManagersStatus::class                        => IsSideEffect::class,
    AppCompCommandHandler\UpdateTaxiPhvStatus::class                                  => IsSideEffect::class,
    AppCompCommandHandler\UpdateCommunityLicencesStatus::class                        => IsSideEffect::class,
    AppCompCommandHandler\UpdateBusinessDetailsStatus::class                          => IsSideEffect::class,
    AppCompCommandHandler\UpdateDeclarationsInternalStatus::class                     => IsSideEffect::class,
    CommandHandler\CommunityLic\GenerateBatch::class                                  => IsSideEffect::class,
    CommandHandler\LicenceStatusRule\RemoveLicenceStatusRulesForLicence::class        => IsSideEffect::class,
    CommandHandler\Email\CreateCorrespondenceRecord::class                            => IsSideEffect::class,
    CommandHandler\Email\SendTmUserCreated::class                                     => IsSideEffect::class,
    CommandHandler\Email\SendUserCreated::class                                       => IsSideEffect::class,
    CommandHandler\Email\SendUserRegistered::class                                    => IsSideEffect::class,
    CommandHandler\Email\SendUserTemporaryPassword::class                             => IsSideEffect::class,
    CommandHandler\Email\SendUsernameSingle::class                                    => IsSideEffect::class,
    CommandHandler\Email\SendUsernameMultiple::class                                  => IsSideEffect::class,
    CommandHandler\Email\SendEbsrWithdrawn::class                                     => IsSideEffect::class,
    CommandHandler\Email\SendEbsrRefused::class                                       => IsSideEffect::class,
    CommandHandler\Email\SendEbsrRegistered::class                                    => IsSideEffect::class,
    CommandHandler\Email\SendEbsrCancelled::class                                     => IsSideEffect::class,
    CommandHandler\Email\SendEbsrReceived::class                                      => IsSideEffect::class,
    CommandHandler\Email\SendEbsrRefreshed::class                                     => IsSideEffect::class,
    CommandHandler\Email\SendEbsrErrors::class                                        => IsSideEffect::class,
    CommandHandler\Email\SendEbsrRequestMap::class                                    => IsSideEffect::class,
    CommandHandler\Email\SendPublication::class                                       => IsSideEffect::class,
    CommandHandler\Person\Create::class                                               => IsSideEffect::class,
    CommandHandler\Person\UpdateFull::class                                           => IsSideEffect::class,
    CommandHandler\PrintScheduler\Enqueue::class                                      => IsSideEffect::class,
    CommandHandler\Vehicle\CreateGoodsVehicle::class                                  => IsSideEffect::class,
    CommandHandler\Vehicle\CeaseActiveDiscs::class                                    => IsSideEffect::class,
    CommandHandler\Vehicle\CreateGoodsDiscs::class                                    => IsSideEffect::class,
    CommandHandler\InspectionRequest\SendInspectionRequest::class                     => IsSideEffect::class,
    CommandHandler\CompaniesHouse\CreateAlert::class                                  => IsSideEffect::class,
    CommandHandler\Discs\CreatePsvVehicleListForDiscs::class                          => IsSideEffect::class,
    CommandHandler\Variation\EndInterim::class                                        => IsSideEffect::class,
    CommandHandler\MyAccount\UpdateMyAccount::class                                   => IsSideEffect::class,
    CommandHandler\System\GenerateSlaTargetDate::class                                => IsSideEffect::class,
    CommandHandler\Bus\Ebsr\CreateTxcInbox::class                                     => IsSideEffect::class,
    CommandHandler\Bus\Ebsr\UpdateTxcInboxPdf::class                                  => IsSideEffect::class,
    QueryHandler\Bus\ByLicenceRoute::class                                            => IsSideEffect::class,
    Dvsa\Olcs\Email\Domain\CommandHandler\UpdateInspectionRequest::class              => IsSideEffect::class,
    Dvsa\Olcs\Email\Domain\CommandHandler\SendEmail::class                            => IsSideEffect::class,
];
