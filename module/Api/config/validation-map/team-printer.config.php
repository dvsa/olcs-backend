<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    QueryHandler\TeamPrinter\TeamPrinterExceptionsList::class               => IsInternalUser::class,
    QueryHandler\TeamPrinter\TeamPrinter::class                             => IsInternalUser::class,
    CommandHandler\TeamPrinter\CreateTeamPrinter::class                     => IsInternalUser::class,
    CommandHandler\TeamPrinter\UpdateTeamPrinter::class                     => IsInternalUser::class,
    CommandHandler\TeamPrinter\DeleteTeamPrinter::class                     => IsInternalUser::class,
];
