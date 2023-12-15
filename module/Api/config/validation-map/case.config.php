<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

/**
 * @NOTE All Case related queries and commands have been moved here and assigned the isInternalUser handler
 */

return [
    //  commands
    CommandHandler\Cases\CloseCase::class                                   => IsInternalUser::class,
    CommandHandler\Cases\Conviction\Create::class                           => IsInternalUser::class,
    CommandHandler\Cases\Conviction\Delete::class                           => IsInternalUser::class,
    CommandHandler\Cases\Conviction\Update::class                           => IsInternalUser::class,
    CommandHandler\Cases\CreateCase::class                                  => IsInternalUser::class,
    CommandHandler\Cases\DeleteCase::class                                  => IsInternalUser::class,
    CommandHandler\Cases\Hearing\CreateAppeal::class                        => IsInternalUser::class,
    CommandHandler\Cases\Hearing\CreateStay::class                          => IsInternalUser::class,
    CommandHandler\Cases\Hearing\UpdateAppeal::class                        => IsInternalUser::class,
    CommandHandler\Cases\Hearing\UpdateStay::class                          => IsInternalUser::class,
    CommandHandler\Cases\Impounding\CreateImpounding::class                 => IsInternalUser::class,
    CommandHandler\Cases\Impounding\DeleteImpounding::class                 => IsInternalUser::class,
    CommandHandler\Cases\Impounding\UpdateImpounding::class                 => IsInternalUser::class,
    CommandHandler\Cases\NonPi\Create::class                                => IsInternalUser::class,
    CommandHandler\Cases\NonPi\Delete::class                                => IsInternalUser::class,
    CommandHandler\Cases\NonPi\Update::class                                => IsInternalUser::class,
    CommandHandler\Cases\Pi\AgreedAndLegislationUpdate::class               => IsInternalUser::class,
    CommandHandler\Cases\Pi\Close::class                                    => IsInternalUser::class,
    CommandHandler\Cases\Pi\CreateAgreedAndLegislation::class               => IsInternalUser::class,
    CommandHandler\Cases\Pi\CreateHearing::class                            => IsInternalUser::class,
    CommandHandler\Cases\Pi\Reopen::class                                   => IsInternalUser::class,
    CommandHandler\Cases\Pi\UpdateDecision::class                           => IsInternalUser::class,
    CommandHandler\Cases\Pi\UpdateTmDecision::class                         => IsInternalUser::class,
    CommandHandler\Cases\Pi\UpdateHearing::class                            => IsInternalUser::class,
    CommandHandler\Cases\Pi\UpdateSla::class                                => IsInternalUser::class,
    CommandHandler\Cases\Prohibition\Create::class                          => IsInternalUser::class,
    CommandHandler\Cases\Prohibition\Defect\Create::class                   => IsInternalUser::class,
    CommandHandler\Cases\Prohibition\Defect\Delete::class                   => IsInternalUser::class,
    CommandHandler\Cases\Prohibition\Defect\Update::class                   => IsInternalUser::class,
    CommandHandler\Cases\Prohibition\Delete::class                          => IsInternalUser::class,
    CommandHandler\Cases\Prohibition\Update::class                          => IsInternalUser::class,
    CommandHandler\Cases\ProposeToRevoke\CreateProposeToRevoke::class       => IsInternalUser::class,
    CommandHandler\Cases\ProposeToRevoke\UpdateProposeToRevoke::class       => IsInternalUser::class,
    CommandHandler\Cases\ProposeToRevoke\UpdateProposeToRevokeSla::class    => IsInternalUser::class,
    CommandHandler\Cases\ReopenCase::class                                  => IsInternalUser::class,
    CommandHandler\Cases\Statement\CreateStatement::class                   => IsInternalUser::class,
    CommandHandler\Cases\Statement\DeleteStatement::class                   => IsInternalUser::class,
    CommandHandler\Cases\Statement\UpdateStatement::class                   => IsInternalUser::class,
    CommandHandler\Cases\UpdateAnnualTestHistory::class                     => IsInternalUser::class,
    CommandHandler\Cases\UpdateCase::class                                  => IsInternalUser::class,
    CommandHandler\Cases\UpdateConvictionNote::class                        => IsInternalUser::class,
    CommandHandler\Cases\UpdateProhibitionNote::class                       => IsInternalUser::class,
    CommandHandler\Cases\UpdatePenaltiesNote::class                         => IsInternalUser::class,
    CommandHandler\TmCaseDecision\CreateDeclareUnfit::class                 => IsInternalUser::class,
    CommandHandler\TmCaseDecision\CreateNoFurtherAction::class              => IsInternalUser::class,
    CommandHandler\TmCaseDecision\CreateReputeNotLost::class                => IsInternalUser::class,
    CommandHandler\TmCaseDecision\Delete::class                             => IsInternalUser::class,
    CommandHandler\TmCaseDecision\UpdateDeclareUnfit::class                 => IsInternalUser::class,
    CommandHandler\TmCaseDecision\UpdateNoFurtherAction::class              => IsInternalUser::class,
    CommandHandler\TmCaseDecision\UpdateReputeNotLost::class                => IsInternalUser::class,
    CommandHandler\Cases\Si\Applied\Delete::class                           => IsInternalUser::class,
    CommandHandler\Cases\Si\Applied\Create::class                           => IsInternalUser::class,
    CommandHandler\Cases\Si\Applied\Update::class                           => IsInternalUser::class,
    CommandHandler\Cases\Si\CreateSi::class                                 => IsInternalUser::class,
    CommandHandler\Cases\Si\DeleteSi::class                                 => IsInternalUser::class,
    CommandHandler\Cases\Si\UpdateSi::class                                 => IsInternalUser::class,
    CommandHandler\Cases\PresidingTc\Create::class                          => IsSystemAdmin::class,
    CommandHandler\Cases\PresidingTc\Update::class                          => IsSystemAdmin::class,
    CommandHandler\Cases\PresidingTc\Delete::class                          => IsSystemAdmin::class,

    //  queries
    QueryHandler\TmCaseDecision\GetByCase::class                            => IsInternalUser::class,
    QueryHandler\Cases\AnnualTestHistory::class                             => IsInternalUser::class,
    QueryHandler\Cases\ByLicence::class                                     => IsInternalUser::class,
    QueryHandler\Cases\ByApplication::class                                 => IsInternalUser::class,
    QueryHandler\Cases\ByTransportManager::class                            => IsInternalUser::class,
    QueryHandler\Cases\Cases::class                                         => IsInternalUser::class,
    QueryHandler\Cases\CasesWithLicence::class                              => IsInternalUser::class,
    QueryHandler\Cases\CasesWithOppositionDates::class                      => IsInternalUser::class,
    QueryHandler\Cases\ConditionUndertaking\ConditionUndertaking::class     => IsInternalUser::class,
    QueryHandler\Cases\ConditionUndertaking\ConditionUndertakingList::class => IsInternalUser::class,
    QueryHandler\Cases\Conviction\Conviction::class                         => IsInternalUser::class,
    QueryHandler\Cases\Conviction\ConvictionList::class                     => IsInternalUser::class,
    QueryHandler\Cases\Hearing\Appeal::class                                => IsInternalUser::class,
    QueryHandler\Cases\Hearing\Stay::class                                  => IsInternalUser::class,
    QueryHandler\Cases\Hearing\StayList::class                              => IsInternalUser::class,
    QueryHandler\Cases\Impounding\Impounding::class                         => IsInternalUser::class,
    QueryHandler\Cases\Impounding\ImpoundingList::class                     => IsInternalUser::class,
    QueryHandler\Cases\LegacyOffence::class                                 => IsInternalUser::class,
    QueryHandler\Cases\LegacyOffenceList::class                             => IsInternalUser::class,
    QueryHandler\Cases\NonPi\Listing::class                                 => IsInternalUser::class,
    QueryHandler\Cases\NonPi\Single::class                                  => IsInternalUser::class,
    QueryHandler\Cases\Pi::class                                            => IsInternalUser::class,
    QueryHandler\Cases\Pi\Hearing::class                                    => IsInternalUser::class,
    QueryHandler\Cases\Pi\HearingList::class                                => IsInternalUser::class,
    QueryHandler\Cases\Pi\ReportList::class                                 => IsInternalUser::class,
    QueryHandler\Cases\Pi\PiDefinitionList::class                           => IsInternalUser::class,
    QueryHandler\Cases\Prohibition\Defect::class                            => IsInternalUser::class,
    QueryHandler\Cases\Prohibition\DefectList::class                        => IsInternalUser::class,
    QueryHandler\Cases\Prohibition\Prohibition::class                       => IsInternalUser::class,
    QueryHandler\Cases\Prohibition\ProhibitionList::class                   => IsInternalUser::class,
    QueryHandler\Cases\ProposeToRevoke\ProposeToRevokeByCase::class         => IsInternalUser::class,
    QueryHandler\Cases\Statement\Statement::class                           => IsInternalUser::class,
    QueryHandler\Cases\Statement\StatementList::class                       => IsInternalUser::class,
    QueryHandler\Organisation\UnlicensedCases::class                        => IsInternalUser::class,
    QueryHandler\Cases\Si\Applied\Penalty::class                            => IsInternalUser::class,
    QueryHandler\Cases\Si\Si::class                                         => IsInternalUser::class,
    QueryHandler\Cases\Si\SiList::class                                     => IsInternalUser::class,
    QueryHandler\Cases\PresidingTc\GetList::class                           => IsInternalUser::class,
    QueryHandler\Cases\PresidingTc\ById::class                              => IsSystemAdmin::class,
    QueryHandler\Cases\Report\OpenList::class                               => IsInternalUser::class,
    QueryHandler\Venue\VenueList::class                                     => IsInternalUser::class,
];
