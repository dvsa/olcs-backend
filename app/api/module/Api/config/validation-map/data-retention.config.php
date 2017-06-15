<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemUser;

return [
    CommandHandler\DataRetention\Populate::class => IsSystemUser::class,
    CommandHandler\DataRetention\RunDelete::class => IsSystemUser::class,
];
