<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsExternalUser;
use Dvsa\Olcs\Api\Domain\QueryHandler;

return [
    CommandHandler\Surrender\Create::class                              => IsExternalUser::class,
    QueryHandler\Surrender\Status::class                                 => IsExternalUser::class
];
