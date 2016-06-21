<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Document\CanAccessDocumentWithId;

return [
    CommandHandler\Document\DeleteDocument::class => CanAccessDocumentWithId::class,
    CommandHandler\Document\DeleteDocuments::class => IsInternalUser::class,
    QueryHandler\Document\Download::class => CanAccessDocumentWithId::class,
];
