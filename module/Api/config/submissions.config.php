<?php
return [
    'excluded-tm-sections' => ['case-summary', 'outstanding-applications', 'people'],
    'section-types' => [
        'submission_type_o_mlh_otc' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_mlh_clo' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_clo_g' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_clo_psv' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_clo_fep' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_otc' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_env' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_irfo' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_bus_reg' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_tm' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_schedule_41' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_impounding' => [
            'case-summary',
            'case-outline',
            'outstanding-applications',
            'people'
        ],
        'submission_type_o_ni_tru' => [
            'case-summary',
            'case-outline',
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
            \Dvsa\Olcs\Api\Service\Submission\Sections\ApplicantsComments::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
            \Dvsa\Olcs\Api\Service\Submission\Sections\ApplicantsResponses::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory::class,
        ],
        'aliases' => [
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
            'applicants-comments' => \Dvsa\Olcs\Api\Service\Submission\Sections\ApplicantsComments::class,
            'applicants-responses' => \Dvsa\Olcs\Api\Service\Submission\Sections\ApplicantsResponses::class,
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
        ],
        'configuration' => [
            'case-summary' => [
                'subcategoryId' => 116,
                'config' => [],
                'section_type' => ['overview'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'case-outline' => [
                'subcategoryId' => 117,
                'config' => [],
                'section_type' => ['text'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'outstanding-applications' => [
                'subcategoryId' => 119,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'most-serious-infringement'   => [
                'subcategoryId' => 118,
                'config' => [],
                'section_type' => ['overview'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'people' => [
                'subcategoryId' => 120,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'operating-centres'   => [
                'subcategoryId' => 121,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'conditions-and-undertakings'   => [
                'subcategoryId' => 122,
                'config' => [],
                'section_type' => ['list'],
                'section_editable' => false,
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'intelligence-unit-check'   => [
                'subcategoryId' => 123,
                'config' => [],
                'section_type' => [],
                'section_editable' => false,
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'interim'   => [
                'subcategoryId' => 124,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'advertisement'   => [
                'subcategoryId' => 125,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'linked-licences-app-numbers'   => [
                'subcategoryId' => 126,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'lead-tc-area'   => [
                'subcategoryId' => 127,
                'config' => [],
                'section_type' => ['text'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'current-submissions'   => [
                'subcategoryId' => 128,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'auth-requested-applied-for'   => [
                'subcategoryId' => 129,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'transport-managers'   => [
                'subcategoryId' => 130,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'continuous-effective-control'   => [
                'subcategoryId' => 131,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'fitness-and-repute'   => [
                'subcategoryId' => 132,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'previous-history'   => [
                'subcategoryId' => 133,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'bus-reg-app-details'   => [
                'subcategoryId' => 134,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'transport-authority-comments'   => [
                'subcategoryId' => 135,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'total-bus-registrations'   => [
                'subcategoryId' => 136,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'local-licence-history'   => [
                'subcategoryId' => 137,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'linked-mlh-history'   => [
                'subcategoryId' => 138,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'registration-details'   => [
                'subcategoryId' => 139,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'maintenance-tachographs-hours'   => [
                'subcategoryId' => 140,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'prohibition-history' => [
                'subcategoryId' => 141,
                'config' => [],
                'section_type' => ['list', 'text'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'conviction-fpn-offence-history' => [
                'subcategoryId' => 142,
                'config' => [],
                'section_type' => ['list', 'text'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'annual-test-history'   => [
                'subcategoryId' => 143,
                'config' => [],
                'section_type' => ['text'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'penalties'   => [
                'subcategoryId' => 144,
                'config' => ['show_multiple_tables_section_header' => false],
                'section_type' => ['list', 'text'],
                'service' => 'Cases',
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'other-issues'   => [
                'subcategoryId' => 146,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'te-reports'   => [
                'subcategoryId' => 147,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'site-plans'   => [
                'subcategoryId' => 148,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'planning-permission'   => [
                'subcategoryId' => 149,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'applicants-responses'   => [
                'subcategoryId' => 181,
                'config' => [],
                'section_type' => ['text'],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'applicants-comments'   => [
                'subcategoryId' => 150,
                'config' => [],
                'section_type' => ['text'],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'visibility-access-egress-size'   => [
                'subcategoryId' => 151,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'compliance-complaints'   => [
                'subcategoryId' => 152,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'environmental-complaints'   => [
                'subcategoryId' => 153,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'oppositions'   => [
                'subcategoryId' => 154,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'financial-information'   => [
                'subcategoryId' => 155,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'maps'   => [
                'subcategoryId' => 156,
                'config' => [],
                'section_type' => ['file'],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'waive-fee-late-fee'   => [
                'subcategoryId' => 157,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'surrender'   => [
                'subcategoryId' => 158,
                'config' => [],
                'section_type' => [],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'annex'   => [
                'subcategoryId' => 159,
                'config' => [],
                'section_type' => ['file'],
                'allow_comments' => true,
                'allow_attachments' => true,
            ],
            'statements'   => [
                'subcategoryId' => 145,
                'config' => [],
                'section_type' => ['list'],
                'service' => 'Cases',
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'tm-details' => [
                'subcategoryId' => 160,
                'config' => [],
                'section_type' => ['overview'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'tm-qualifications' => [
                'subcategoryId' => 161,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'tm-responsibilities' => [
                'subcategoryId' => 162,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'tm-other-employment' => [
                'subcategoryId' => 163,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ],
            'tm-previous-history' => [
                'subcategoryId' => 164,
                'config' => [],
                'section_type' => ['list'],
                'allow_comments' => true,
                'allow_attachments' => true
            ]
        ]
    ]
];
