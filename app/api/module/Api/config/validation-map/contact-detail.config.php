<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    QueryHandler\ContactDetail\CountryList::class               => NoValidationRequired::class,
    QueryHandler\ContactDetail\ContactDetailsList::class        => IsInternalUser::class,
];
