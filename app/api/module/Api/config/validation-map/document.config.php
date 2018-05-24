<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanAccessDocumentWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanCreateDocument;

return [
    CommandHandler\Document\DeleteDocument::class => CanAccessDocumentWithId::class,
    CommandHandler\Document\DeleteDocuments::class => IsInternalUser::class,
    CommandHandler\Document\CreateDocument::class => CanCreateDocument::class,
    CommandHandler\Document\Upload::class => CanCreateDocument::class,
    CommandHandler\Document\CopyDocument::class => IsInternalUser::class,
    CommandHandler\Document\CreateLetter::class => IsInternalUser::class,
    CommandHandler\Document\GenerateAndStore::class => IsInternalUser::class,
    CommandHandler\Document\GenerateAndStoreWithMultipleAddresses::class => IsSystemUser::class,
    CommandHandler\Document\MoveDocument::class => IsInternalUser::class,
    CommandHandler\Document\PrintLetter::class => CanAccessDocumentWithId::class,
    CommandHandler\Document\UpdateDocumentLinks::class => IsInternalUser::class,
    CommandHandler\Document\RemoveDeletedDocuments::class => IsSystemUser::class,
    CommandHandler\Email\SendPsvOperatorListReport::class => CanAccessDocumentWithId::class,
    CommandHandler\Email\SendInternationalGoods::class => CanAccessDocumentWithId::class,

    //  queries
    QueryHandler\Document\Download::class => CanAccessDocumentWithId::class,
    QueryHandler\Document\DownloadGuide::class => NoValidationRequired::class,
    QueryHandler\Document\PrintLetter::class => CanAccessDocumentWithId::class,
];
