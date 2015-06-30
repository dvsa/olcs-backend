<?php

return [
    'service_manager' => [
        'invokables' => [
            'ReviewSnapshot' => \Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Generator::class,
        ],
    ],
];
