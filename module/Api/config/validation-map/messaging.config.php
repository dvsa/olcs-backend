<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    QueryHandler\Messaging\Conversations\ByLicence::class => NoValidationRequired::class,
    QueryHandler\Messaging\Conversations\ByApplicationToLicence::class => NoValidationRequired::class,
    CommandHandler\Messaging\CreateMessage::class => NoValidationRequired::class,
];
