<?php

use Dvsa\Olcs\Api\Domain\Validation\Validators;

return [
    'factories' => [
        'isOwner' => Validators\IsOwner::class,
        'doesOwnLicence' => Validators\DoesOwnLicence::class,
        'doesOwnApplication' => Validators\DoesOwnApplication::class,
        'doesOwnCompanySubsidiary' => Validators\DoesOwnCompanySubsidiary::class,
        'canAccessLicence' => Validators\CanAccessLicence::class,
        'canAccessApplication' => Validators\CanAccessApplication::class,
        'canAccessCompanySubsidiary' => Validators\CanAccessCompanySubsidiary::class,
    ]
];
