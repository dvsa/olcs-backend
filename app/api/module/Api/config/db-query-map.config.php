<?php

use Dvsa\Olcs\Api\Domain\Repository\Query;

return [
    'factories' => [
        'LicenceVehicle\CeaseDiscsForLicence' => Query\LicenceVehicle\CeaseDiscsForLicence::class,
        'LicenceVehicle\CreateDiscsForLicence' => Query\LicenceVehicle\CreateDiscsForLicence::class,
        'CommunityLicence\ExpireAllForLicence' => Query\CommunityLicence\ExpireAllForLicence::class,
        'LicenceVehicle\ClearSpecifiedDateAndInterimAppForLicence'
            => Query\LicenceVehicle\ClearSpecifiedDateAndInterimAppForLicence::class,
        'LicenceVehicle\RemoveAllForLicence' => Query\LicenceVehicle\RemoveAllForLicence::class,
        'LicenceVehicle\MarkDuplicateVrmsForLicence' => Query\LicenceVehicle\MarkDuplicateVrmsForLicence::class,
        'LicenceVehicle\ClearVehicleSection26' => Query\LicenceVehicle\ClearVehicleSection26::class,
        'Discs\CeaseDiscsForLicence' => Query\Discs\CeaseDiscsForLicence::class,
        'Discs\CreatePsvDiscs' => Query\Discs\CreatePsvDiscs::class,
        'Discs\GoodsDiscsSetIsPrinting' => Query\Discs\GoodsDiscsSetIsPrinting::class,
        'Discs\GoodsDiscsSetIsPrintingOffAndDiscNo' => Query\Discs\GoodsDiscsSetIsPrintingOffAndDiscNo::class,
        'Discs\PsvDiscsSetIsPrinting' => Query\Discs\PsvDiscsSetIsPrinting::class,
        'Discs\PsvDiscsSetIsPrintingOffAndDiscNo' => Query\Discs\PsvDiscsSetIsPrintingOffAndDiscNo::class,
        'Discs\CeaseGoodsDiscsForApplication' => Query\Discs\CeaseGoodsDiscsForApplication::class,
        'Discs\CreateGoodsDiscs' => Query\Discs\CreateGoodsDiscs::class,
        'ViStoredProcedures\ViOcComplete' => Query\ViStoredProcedures\ViOcComplete::class,
        'ViStoredProcedures\ViOpComplete' => Query\ViStoredProcedures\ViOpComplete::class,
        'ViStoredProcedures\ViTnmComplete' => Query\ViStoredProcedures\ViTnmComplete::class,
        'ViStoredProcedures\ViVhlComplete' => Query\ViStoredProcedures\ViVhlComplete::class,
    ]
];
