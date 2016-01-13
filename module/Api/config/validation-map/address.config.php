<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    QueryHandler\Address\GetAddress::class => NoValidationRequired::class,
    QueryHandler\Address\GetList::class => NoValidationRequired::class,
];
