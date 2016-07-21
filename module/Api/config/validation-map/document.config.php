<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanAccessDocumentWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanCreateDocument;

return [
    CommandHandler\Document\DeleteDocument::class => CanAccessDocumentWithId::class,
    CommandHandler\Document\DeleteDocuments::class => IsInternalUser::class,
    QueryHandler\Document\Download::class => CanAccessDocumentWithId::class,
    // No validation as these a public documents
    QueryHandler\Document\DownloadGuide::class => NoValidationRequired::class,
    CommandHandler\Document\CreateDocument::class => CanCreateDocument::class,
    CommandHandler\Document\Upload::class => CanCreateDocument::class,
];
