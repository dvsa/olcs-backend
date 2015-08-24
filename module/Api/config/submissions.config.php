<?php
return [
    'section-types' => [
        'submission_type_o_mlh_otc' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_mlh_clo' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_clo_g' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_clo_psv' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_clo_fep' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_otc' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_env' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_irfo' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_bus_reg' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_tm' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_schedule_41' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_impounding' => [
            'introduction',
            'case-summary',
            'case-outline',
            'most-serious-infringement',
            'outstanding-applications',
            'people'
        ],

    ],
    'sections' => [
        'factories' => [
            \Dvsa\Olcs\Api\Service\Submission\Sections\Introduction::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\CaseSummary::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\CaseOutline::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\MostSeriousInfringement::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\OutstandingApplications::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\People::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\OperatingCentres::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\ConditionsAndUndertakings::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\IntelligenceUnitCheck::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\Interim::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\LinkedLicences::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class
        ],
        'aliases' => [
            'introduction' => \Dvsa\Olcs\Api\Service\Submission\Sections\Introduction::class,
            'case-summary' => \Dvsa\Olcs\Api\Service\Submission\Sections\CaseSummary::class,
            'case-outline' => \Dvsa\Olcs\Api\Service\Submission\Sections\CaseOutline::class,
            'most-serious-infringement' => \Dvsa\Olcs\Api\Service\Submission\Sections\MostSeriousInfringement::class,
            'outstanding-applications' => \Dvsa\Olcs\Api\Service\Submission\Sections\OutstandingApplications::class,
            'people' => \Dvsa\Olcs\Api\Service\Submission\Sections\People::class,
            'operating-centres' => \Dvsa\Olcs\Api\Service\Submission\Sections\OperatingCentres::class,
            'conditions-and-undertakings' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\ConditionsAndUndertakings::class,
            'intelligence-unit-check' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\IntelligenceUnitCheck::class,
            'interim' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\Interim::class,
            'linked-licences-app-numbers' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\LinkedLicences::class,
        ]
    ]
];
