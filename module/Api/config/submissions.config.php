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
            \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\LinkedLicences::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\LeadTcArea::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\AuthRequestedAppliedFor::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\TransportManagers::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\ProhibitionHistory::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\ConvictionFpnOffenceHistory::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\AnnualTestHistory::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\Penalties::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\Statements::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\EnvironmentalComplaints::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\ComplianceComplaints::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\Oppositions::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\TmDetails::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\TmResponsibilities::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\TmQualifications::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\TmOtherEmployment::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\TmPreviousHistory::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
        ],
        'aliases' => [
            'introduction' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'case-summary' => \Dvsa\Olcs\Api\Service\Submission\Sections\CaseSummary::class,
            'case-outline' => \Dvsa\Olcs\Api\Service\Submission\Sections\CaseOutline::class,
            'most-serious-infringement' => \Dvsa\Olcs\Api\Service\Submission\Sections\MostSeriousInfringement::class,
            'outstanding-applications' => \Dvsa\Olcs\Api\Service\Submission\Sections\OutstandingApplications::class,
            'people' => \Dvsa\Olcs\Api\Service\Submission\Sections\People::class,
            'operating-centres' => \Dvsa\Olcs\Api\Service\Submission\Sections\OperatingCentres::class,
            'conditions-and-undertakings' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\ConditionsAndUndertakings::class,
            'intelligence-unit-check' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'interim' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'advertisement' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'linked-licences-app-numbers' => \Dvsa\Olcs\Api\Service\Submission\Sections\LinkedLicences::class,
            'lead-tc-area' => \Dvsa\Olcs\Api\Service\Submission\Sections\LeadTcArea::class,
            'current-submissions' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'auth-requested-applied-for' => \Dvsa\Olcs\Api\Service\Submission\Sections\AuthRequestedAppliedFor::class,
            'transport-managers' => \Dvsa\Olcs\Api\Service\Submission\Sections\TransportManagers::class,
            'continuous-effective-control' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'fitness-and-repute' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'previous-history' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'bus-reg-app-details' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'transport-authority-comments' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'total-bus-registrations' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'local-licence-history' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'linked-mlh-history' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'registration-details' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'maintenance-tachographs-hours' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'prohibition-history' => \Dvsa\Olcs\Api\Service\Submission\Sections\ProhibitionHistory::class,
            'conviction-fpn-offence-history' =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\ConvictionFpnOffenceHistory::class,
            'annual-test-history' => \Dvsa\Olcs\Api\Service\Submission\Sections\AnnualTestHistory::class,
            'penalties' => \Dvsa\Olcs\Api\Service\Submission\Sections\Penalties::class,
            'other-issues' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'te-reports' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'site-plans' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'planning-permission' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'applicants-comments' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'visibility-access-egress-size' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'compliance-complaints' => \Dvsa\Olcs\Api\Service\Submission\Sections\ComplianceComplaints::class,
            'environmental-complaints' => \Dvsa\Olcs\Api\Service\Submission\Sections\EnvironmentalComplaints::class,
            'oppositions' => \Dvsa\Olcs\Api\Service\Submission\Sections\Oppositions::class,
            'financial-information' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'maps' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'waive-fee-late-fee' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'surrender' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'annex' => \Dvsa\Olcs\Api\Service\Submission\Sections\NoDataCommentsOnly::class,
            'statements' => \Dvsa\Olcs\Api\Service\Submission\Sections\Statements::class,
            'tm-details' => \Dvsa\Olcs\Api\Service\Submission\Sections\TmDetails::class,
            'tm-responsibilities' => \Dvsa\Olcs\Api\Service\Submission\Sections\TmResponsibilities::class,
            'tm-qualifications' => \Dvsa\Olcs\Api\Service\Submission\Sections\TmQualifications::class,
            'tm-other-employment' => \Dvsa\Olcs\Api\Service\Submission\Sections\TmOtherEmployment::class,
            'tm-previous-history' => \Dvsa\Olcs\Api\Service\Submission\Sections\TmPreviousHistory::class,
        ]
    ]
];
