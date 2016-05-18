<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;
use Dvsa\Olcs\Cli\Domain\CommandHandler as CliCommandHandler;

return [
    CliCommandHandler\CreateViExtractFiles::class => IsSystemUser::class,
    CliCommandHandler\SetViFlags::class => IsSystemUser::class,
];
