<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsExternalUser;

return [
    CommandHandler\Surrender\Create::class                              => IsExternalUser::class,
];
