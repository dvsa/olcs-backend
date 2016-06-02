<?php

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;
use Dvsa\Olcs\Cli\Domain\CommandHandler as CliCommandHandler;

return [
    CliCommandHandler\CreateViExtractFiles::class => IsSystemUser::class,
    CliCommandHandler\SetViFlags::class => IsSystemUser::class,
    CliCommandHandler\DataGovUkExport::class => IsSystemUser::class,
];
