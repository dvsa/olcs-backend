<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application as AppCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence as LicCommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\People\Application as AppHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\People\Licence as LicHandler;

// @todo next story
//return [
//    AppCommandHandler\CreatePeople::class => AppHandler\Create::class,
//    AppCommandHandler\DeletePeople::class => Handler\Standard::class, // @todo
//    AppCommandHandler\RestorePeople::class => Handler\Standard::class, // @todo
//    AppCommandHandler\UpdatePeople::class => Handler\Standard::class, // @todo
//
//    LicCommandHandler\CreatePeople::class => LicHandler\Create::class,
//    LicCommandHandler\DeletePeople::class => Handler\Standard::class, // @todo
//    LicCommandHandler\UpdatePeople::class => Handler\Standard::class, // @todo
//
//    QueryHandler\Licence\People::class => Handler\Standard::class, // @todo
//    QueryHandler\Application\People::class => Handler\Standard::class, // @todo
//    QueryHandler\Organisation\People::class => Handler\Standard::class, // @todo
//];

return [
    AppCommandHandler\CreatePeople::class => Handler\Standard::class, // @todo
    AppCommandHandler\DeletePeople::class => Handler\Standard::class, // @todo
    AppCommandHandler\RestorePeople::class => Handler\Standard::class, // @todo
    AppCommandHandler\UpdatePeople::class => Handler\Standard::class, // @todo

    LicCommandHandler\CreatePeople::class => Handler\Standard::class, // @todo
    LicCommandHandler\DeletePeople::class => Handler\Standard::class, // @todo
    LicCommandHandler\UpdatePeople::class => Handler\Standard::class, // @todo

    QueryHandler\Licence\People::class => Handler\Standard::class, // @todo
    QueryHandler\Application\People::class => Handler\Standard::class, // @todo
    QueryHandler\Organisation\People::class => Handler\Standard::class, // @todo
];
