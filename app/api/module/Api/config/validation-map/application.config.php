<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalOrSystemUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessApplicationWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessOrganisationWithOrganisation;

return [
    CommandHandler\Application\Schedule41Approve::class => IsInternalUser::class,
    CommandHandler\Application\Schedule41Refuse::class  => IsInternalUser::class,
    CommandHandler\Application\Schedule41Cancel::class  => IsInternalOrSystemUser::class,
    CommandHandler\Application\Schedule41Reset::class   => IsInternalUser::class,
    CommandHandler\Application\NotTakenUpApplication::class => IsInternalOrSystemUser::class,
    CommandHandler\Application\CancelApplication::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\CreateApplication::class => CanAccessOrganisationWithOrganisation::class,
    CommandHandler\Application\CreateSnapshot::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\CreateTaxiPhv::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\DeleteTaxiPhv::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\GenerateOrganisationName::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\SubmitApplication::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateAddresses::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateApplicationCompletion::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateBusinessDetails::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateDeclaration::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateFinancialEvidence::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateFinancialHistory::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateTaxiPhv::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdateTypeOfLicence::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\WithdrawApplication::class => CanAccessApplicationWithId::class,
    CommandHandler\Application\UpdatePrivateHireLicence::class => CanAccessApplicationWithId::class,

    QueryHandler\Application\OutstandingFees::class     => CanAccessApplicationWithId::class,
    QueryHandler\Application\Application::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\Declaration::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\DeclarationUndertakings::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\FinancialEvidence::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\FinancialHistory::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\Review::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\Schedule41Approve::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\Summary::class => CanAccessApplicationWithId::class,
    QueryHandler\Application\TaxiPhv::class => CanAccessApplicationWithId::class,
];
