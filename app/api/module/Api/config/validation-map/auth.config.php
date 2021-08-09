<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    CommandHandler\Auth\Login::class => NoValidationRequired::class,
    CommandHandler\Auth\LoginFactory::class => NoValidationRequired::class,
];
