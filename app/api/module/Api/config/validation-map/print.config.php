<?php

use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

/**
 * @NOTE All Case related queries and commands have been moved here and assigned the isInternalUser handler
 */
return [
    PrintScheduler\PrintJob::class => NoValidationRequired::class
];
