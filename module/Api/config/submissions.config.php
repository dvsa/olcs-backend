<?php
return [
    'context' => [
        'factories' => [
        Dvsa\Olcs\Api\Service\Submission\Context\Sections\CaseSummary::class =>
            Dvsa\Olcs\Api\Service\Submission\Context\AbstractFactory::class,]
    ],
    'process' => [
    ],
    'section-types' => [
        'submission_type_o_mlh_otc' => [
            'case-summary',
        ]
    ],
    'sections' => [
        'case-summary' => array(
            'context' => [
                \Dvsa\Olcs\Api\Service\Submission\Context\Sections\CaseSummary::class
            ],
            'process' => [
            ],
        )
    ]
];




