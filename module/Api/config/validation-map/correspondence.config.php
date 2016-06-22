<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\CorrespondenceInbox\CanAccessCorrespondenceInboxWithId;

return [
    CommandHandler\Correspondence\AccessCorrespondence::class => CanAccessCorrespondenceInboxWithId::class,
    QueryHandler\Correspondence\Correspondence::class => CanAccessCorrespondenceInboxWithId::class,
];
