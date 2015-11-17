<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application as AppCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence as LicCommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Standard;

return [
    AppCommandHandler\CreatePeople::class   => Misc\CanAccessApplicationWithId::class,
    AppCommandHandler\DeletePeople::class   => Handler\People\Application\Modify::class,
    AppCommandHandler\RestorePeople::class  => Handler\People\Application\Modify::class,
    AppCommandHandler\UpdatePeople::class   => Handler\People\Application\Update::class,
    LicCommandHandler\CreatePeople::class   => Misc\CanAccessLicenceWithId::class,
    LicCommandHandler\DeletePeople::class   => Handler\People\Licence\Modify::class,
    LicCommandHandler\UpdatePeople::class   => Handler\People\Licence\Update::class,
    QueryHandler\Licence\People::class      => Misc\CanAccessLicenceWithId::class,
    QueryHandler\Application\People::class  => Misc\CanAccessApplicationWithId::class,
    QueryHandler\Organisation\People::class => Misc\CanAccessOrganisationWithId::class,

    CommandHandler\OrganisationPerson\Create::class                     => Standard::class, // @todo
    CommandHandler\OrganisationPerson\DeleteList::class                 => Standard::class, // @todo
    CommandHandler\OrganisationPerson\PopulateFromCompaniesHouse::class => Standard::class, // @todo
    CommandHandler\OrganisationPerson\Update::class                     => Standard::class, // @todo
    CommandHandler\Person\Create::class                                 => Standard::class, // @todo
    CommandHandler\Person\Update::class                                 => Standard::class, // @todo
    CommandHandler\Person\UpdateFull::class                             => Standard::class, // @todo
    QueryHandler\OrganisationPerson\GetSingle::class                    => Standard::class, // @todo
    QueryHandler\Person\Person::class                                   => Standard::class, // @todo
];
