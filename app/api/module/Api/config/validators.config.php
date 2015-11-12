<?php

use Dvsa\Olcs\Api\Domain\Validation\Validators;

return [
    'invokables' => [
        'isOwner' => Validators\IsOwner::class,
    ]
];
