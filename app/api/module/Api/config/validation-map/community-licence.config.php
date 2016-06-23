<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Cli\Domain\QueryHandler as QueryHandlerCli;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Cli\Domain\CommandHandler as CommandHandlerCli;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;

return [
    QueryHandler\CommunityLic\CommunityLicences::class                     => IsInternalUser::class,
    QueryHandler\CommunityLic\CommunityLicence::class                      => IsInternalUser::class,
    QueryHandlerCli\CommunityLic\CommunityLicencesForSuspensionList::class => IsSystemUser::class,
    QueryHandlerCli\CommunityLic\CommunityLicencesForActivationList::class => IsSystemUser::class,
    CommandHandler\CommunityLic\EditSuspension::class                      => IsInternalUser::class,
    CommandHandlerCli\CommunityLic\Activate::class                         => IsSystemUser::class,
    CommandHandlerCli\CommunityLic\Suspend::class                          => IsSystemUser::class,
];
