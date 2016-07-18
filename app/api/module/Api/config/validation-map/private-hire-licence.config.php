<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;

return [
    // Commands
    CommandHandler\PrivateHireLicence\Create::class          => PrivateHireLicence::class,
    CommandHandler\PrivateHireLicence\DeleteList::class      => PrivateHireLicence::class,
    CommandHandler\PrivateHireLicence\Update::class          => PrivateHireLicence::class,
];
