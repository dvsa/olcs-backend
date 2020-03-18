<?php

use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section as Review;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section as TmReview;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section as ContinuationReview;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Permits\IrhpGenerator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Permits\IrhpGeneratorFactory;

return [
    'service_manager' => [
        'invokables' => [
            'ReviewSnapshot' => \Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Generator::class,
            'Review\VariationTypeOfLicence' => Review\VariationTypeOfLicenceReviewService::class,
            'Review\VariationBusinessType' => Review\VariationBusinessTypeReviewService::class,
            'Review\AbstractVariationOcTotalAuth' => Review\AbstractVariationOcTotalAuthReviewService::class,
            'Review\VariationFinancialEvidence' => Review\VariationFinancialEvidenceReviewService::class,
            'Review\ApplicationFinancialEvidence' => Review\ApplicationFinancialEvidenceReviewService::class,
            'Review\VariationLicenceHistory' => Review\VariationLicenceHistoryReviewService::class,
            'Review\ApplicationVehiclesDeclarations' => Review\ApplicationVehiclesDeclarationsReviewService::class,
            'Review\ApplicationSafety' => Review\ApplicationSafetyReviewService::class,
            'Review\VariationBusinessDetails' => Review\VariationBusinessDetailsReviewService::class,
            'Review\VariationAddresses' => Review\VariationAddressesReviewService::class,
            'Review\ApplicationOperatingCentres' => Review\ApplicationOperatingCentresReviewService::class,
            'Review\ReviewServiceInterface' => Review\ReviewServiceInterface::class,
            'Review\VariationPsvOcTotalAuth' => Review\VariationPsvOcTotalAuthReviewService::class,
            'Review\VariationSafety' => Review\VariationSafetyReviewService::class,
            'Review\VariationVehiclesDeclarations' => Review\VariationVehiclesDeclarationsReviewService::class,
            'Review\ApplicationBusinessDetails' => Review\ApplicationBusinessDetailsReviewService::class,
            'Review\ApplicationTypeOfLicence' => Review\ApplicationTypeOfLicenceReviewService::class,
            'Review\GoodsOperatingCentre' => Review\GoodsOperatingCentreReviewService::class,
            'Review\ApplicationConditionsUndertakings' => Review\ApplicationConditionsUndertakingsReviewService::class,
            'Review\ConditionsUndertakings' => Review\ConditionsUndertakingsReviewService::class,
            'Review\ApplicationGoodsOcTotalAuth' => Review\ApplicationGoodsOcTotalAuthReviewService::class,
            'Review\VariationPeople' => Review\VariationPeopleReviewService::class,
            'Review\VehiclesPsv' => Review\VehiclesPsvReviewService::class,
            'Review\ApplicationPsvOcTotalAuth' => Review\ApplicationPsvOcTotalAuthReviewService::class,
            'Review\Abstract' => Review\AbstractReviewService::class,
            'Review\VariationConditionsUndertakings' => Review\VariationConditionsUndertakingsReviewService::class,
            'Review\ApplicationBusinessType' => Review\ApplicationBusinessTypeReviewService::class,
            'Review\VariationOperatingCentres' => Review\VariationOperatingCentresReviewService::class,
            'Review\ApplicationFinancialHistory' => Review\ApplicationFinancialHistoryReviewService::class,
            'Review\VariationDiscs' => Review\VariationDiscsReviewService::class,
            'Review\VariationTransportManagers' => Review\VariationTransportManagersReviewService::class,
            'Review\PsvOperatingCentre' => Review\PsvOperatingCentreReviewService::class,
            'Review\VariationConvictionsPenalties' => Review\VariationConvictionsPenaltiesReviewService::class,
            'Review\ApplicationLicenceHistory' => Review\ApplicationLicenceHistoryReviewService::class,
            'Review\ApplicationPeople' => Review\ApplicationPeopleReviewService::class,
            'Review\TransportManagers' => Review\TransportManagersReviewService::class,
            'Review\TrafficArea' => Review\TrafficAreaReviewService::class,
            'Review\VariationFinancialHistory' => Review\VariationFinancialHistoryReviewService::class,
            'Review\LicenceConditionsUndertakings' => Review\LicenceConditionsUndertakingsReviewService::class,
            'Review\VariationVehiclesPsv' => Review\VariationVehiclesPsvReviewService::class,
            'Review\ApplicationVehicles' => Review\ApplicationVehiclesReviewService::class,
            'Review\ApplicationConvictionsPenalties' => Review\ApplicationConvictionsPenaltiesReviewService::class,
            'Review\ApplicationTransportManagers' => Review\ApplicationTransportManagersReviewService::class,
            'Review\VariationGoodsOcTotalAuth' => Review\VariationGoodsOcTotalAuthReviewService::class,
            'Review\People' => Review\PeopleReviewService::class,
            'Review\ApplicationVehiclesPsv' => Review\ApplicationVehiclesPsvReviewService::class,
            'Review\ApplicationAddresses' => Review\ApplicationAddressesReviewService::class,
            'Review\ApplicationTaxiPhv' => Review\ApplicationTaxiPhvReviewService::class,
            'Review\VariationVehicles' => Review\VariationVehiclesReviewService::class,
            'Review\ApplicationUndertakings' => Review\ApplicationUndertakingsReviewService::class,
            'Review\VariationUndertakings' => Review\VariationUndertakingsReviewService::class,
            Review\SignatureReviewService::class => Review\SignatureReviewService::class,
            'TmReviewSnapshot' => \Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Generator::class,
            'Review\TransportManagerMain' => TmReview\TransportManagerMainReviewService::class,
            'Review\TransportManagerResponsibility' => TmReview\TransportManagerResponsibilityReviewService::class,
            'Review\TransportManagerOtherEmployment' => TmReview\TransportManagerOtherEmploymentReviewService::class,
            'Review\TransportManagerPreviousConviction'
                => TmReview\TransportManagerPreviousConvictionReviewService::class,
            'Review\TransportManagerPreviousLicence' => TmReview\TransportManagerPreviousLicenceReviewService::class,
            'Review\TransportManagerDeclaration' => TmReview\TransportManagerDeclarationReviewService::class,
            'Review\TransportManagerSignature' => TmReview\TransportManagerSignatureReviewService::class,
            'ContinuationReview' => \Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Generator::class,
            'ContinuationReview\TypeOfLicence' => ContinuationReview\TypeOfLicenceReviewService::class,
            'ContinuationReview\BusinessType' => ContinuationReview\BusinessTypeReviewService::class,
            'ContinuationReview\BusinessDetails' => ContinuationReview\BusinessDetailsReviewService::class,
            'ContinuationReview\Addresses' => ContinuationReview\AddressesReviewService::class,
            'ContinuationReview\People' => ContinuationReview\PeopleReviewService::class,
            'ContinuationReview\Vehicles' => ContinuationReview\VehiclesReviewService::class,
            'ContinuationReview\Users' => ContinuationReview\UsersReviewService::class,
            'ContinuationReview\VehiclesPsv' => ContinuationReview\VehiclesReviewService::class,
            'ContinuationReview\OperatingCentres' => ContinuationReview\OperatingCentresReviewService::class,
            'ContinuationReview\TransportManagers' => ContinuationReview\TransportManagersReviewService::class,
            'ContinuationReview\Safety' => ContinuationReview\SafetyReviewService::class,
            'ContinuationReview\Declaration' => ContinuationReview\DeclarationReviewService::class,
            'ContinuationReview\Finance' => ContinuationReview\FinanceReviewService::class,
            'ContinuationReview\ConditionsUndertakings' =>
                ContinuationReview\ConditionsUndertakingsReviewService::class,
            Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Generator::class => \Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Generator::class,
            Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\LicenceDetailsService::class => \Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\LicenceDetailsService::class,
            Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CurrentDiscsReviewService::class => \Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CurrentDiscsReviewService::class,
            Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\OperatorLicenceReviewService::class => \Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\OperatorLicenceReviewService::class,
            Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\DeclarationReviewService::class => \Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\DeclarationReviewService::class,
            Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\SignatureReviewService::class => \Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\SignatureReviewService::class,
            Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CommunityLicenceReviewService::class=>\Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CommunityLicenceReviewService::class,
        ],
        'factories' => [
            IrhpGenerator::class => IrhpGeneratorFactory::class,
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
            'translations' => __DIR__ . '/language/partials'
        ]
    ],
    'view_helpers' => [
        'invokables' => [
            'answerFormatter' => Dvsa\Olcs\Snapshot\View\Helper\AnswerFormatter::class,
        ],
    ],
];
