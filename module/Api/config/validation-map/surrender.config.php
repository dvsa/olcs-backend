<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsExternalUser;

return [
    CommandHandler\Surrender\Create::class                              => IsExternalUser::class,
    CommandHandler\Surrender\Update::class                              => IsExternalUser::class,
    CommandHandler\Surrender\Delete::class                              => IsInternalUser::class,
];
