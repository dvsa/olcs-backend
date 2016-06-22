<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    QueryHandler\CommunityLic\CommunityLicences::class => IsInternalUser::class,
    QueryHandler\CommunityLic\CommunityLicence::class  => IsInternalUser::class,
    CommandHandler\CommunityLic\EditSuspension::class  => IsInternalUser::class,
];
