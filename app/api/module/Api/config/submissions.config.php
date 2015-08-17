<?php
return [
    'section-types' => [
        'submission_type_o_mlh_otc' => [
            'case-summary'
        ]
    ],
    'sections' => [
        'factories' => [
            \Dvsa\Olcs\Api\Service\Submission\Sections\CaseSummary::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class
        ],
        'aliases' => [
            'case-summary' => \Dvsa\Olcs\Api\Service\Submission\Sections\CaseSummary::class,
        ]
    ]
];




