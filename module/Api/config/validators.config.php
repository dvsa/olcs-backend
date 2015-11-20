<?php

use Dvsa\Olcs\Api\Domain\Validation\Validators;

return [
    'factories' => [
        'isOwner' => Validators\IsOwner::class,

        'doesOwnLicence' => Validators\DoesOwnLicence::class,
        'doesOwnApplication' => Validators\DoesOwnApplication::class,
        'doesOwnCompanySubsidiary' => Validators\DoesOwnCompanySubsidiary::class,
        'doesOwnOrganisation' => Validators\DoesOwnOrganisation::class,
        'doesOwnOrganisationPerson' => Validators\DoesOwnOrganisationPerson::class,

        'canAccessLicence' => Validators\CanAccessLicence::class,
        'canAccessApplication' => Validators\CanAccessApplication::class,
        'canAccessCompanySubsidiary' => Validators\CanAccessCompanySubsidiary::class,
        'canAccessOrganisation' => Validators\CanAccessOrganisation::class,
        'canAccessOrganisationPerson' => Validators\CanAccessOrganisationPerson::class,
        'canAccessTransportManagerApplication' => Validators\CanAccessTransportManagerApplication::class,
        'CanAccessPreviousConviction' => Validators\CanAccessPreviousConviction::class,
        'canAccessTrailer' => Validators\CanAccessTrailer::class,
    ]
];
