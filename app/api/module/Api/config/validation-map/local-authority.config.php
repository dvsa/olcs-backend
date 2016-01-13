<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    // Queries
    QueryHandler\LocalAuthority\LocalAuthorityList::class => IsInternalUser::class,
];
