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

    CommandHandler\OrganisationPerson\Create::class  => Misc\CanAccessOrganisationWithOrganisation::class,

    // No validation can be applied to these, as they are side-effect commands designed to be re-used
    CommandHandler\Person\Create::class              => Misc\NoValidationRequired::class,
    CommandHandler\Person\UpdateFull::class          => Misc\NoValidationRequired::class,
    QueryHandler\OrganisationPerson\GetSingle::class => Misc\CanAccessOrganisationPersonWithId::class,

    CommandHandler\OrganisationPerson\PopulateFromCompaniesHouse::class => Misc\CanAccessOrganisationWithId::class,

    CommandHandler\Person\Update::class                 => Standard::class, // @todo
    CommandHandler\OrganisationPerson\DeleteList::class => Standard::class, // @todo
    CommandHandler\OrganisationPerson\Update::class     => Standard::class, // @todo
    QueryHandler\Person\Person::class                   => Standard::class, // @todo
];
