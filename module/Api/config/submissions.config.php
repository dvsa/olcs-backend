<?php
return [
    'context' => [
        Dvsa\Olcs\Api\Service\Submission\Context\Sections\CaseSummary::class =>
            Dvsa\Olcs\Api\Service\Submission\Context\AbstractFactory::class,
    ],
    'process' => [
        Dvsa\Olcs\Api\Service\Submission\Process\Text1::class =>
            Dvsa\Olcs\Api\Service\Submission\Process\Text1::class,
        
    ],
    'section-types' => [
        'submission_type_o_mlh_otc' => [
            'case-summary',
        ],
        'submission_type_o_mlh_clo' => [

        ],
        'submission_type_o_clo_g' => [

        ],
        'submission_type_o_clo_psv' => [

        ],
        'submission_type_o_clo_fep' => [

        ],
        'submission_type_o_otc' => [

        ],
        'submission_type_o_env' => [

        ],
        'submission_type_o_irfo' => [
        ],
        'submission_type_o_bus_reg' => [

        ],
        'submission_type_o_tm' => [

        ],
        'submission_type_o_schedule_41' => [

        ],
        'submission_type_o_impounding' => [

        ]
    ],
    'sections' => [
        'ApplicationSubmission' => array(
            'context' => [
                /*Dvsa\Olcs\Api\Service\Submission\Context\Application\BusNote::class,
                Dvsa\Olcs\Api\Service\Submission\Context\Submission\PreviousApplicationSubmissionNo::class,
                Dvsa\Olcs\Api\Service\Submission\Context\Application\ConditionUndertaking::class,
                Dvsa\Olcs\Api\Service\Submission\Context\Application\LicenceCancelled::class,
                Dvsa\Olcs\Api\Service\Submission\Context\Application\OperatingCentres::class,
                Dvsa\Olcs\Api\Service\Submission\Context\Application\TransportManagers::class,
                Dvsa\Olcs\Api\Service\Submission\Context\Licence\LicenceAddress::class,*/
            ],
            'process' => [
            ],
        )
    ]
];




