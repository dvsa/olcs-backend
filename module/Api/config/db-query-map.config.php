<?php

use Dvsa\Olcs\Api\Domain\Repository\Query;

return [
    'factories' => [
        'LicenceVehicle\CeaseDiscsForLicence' => Query\LicenceVehicle\CeaseDiscsForLicence::class,
        'CommunityLicence\ExpireAllForLicence' => Query\CommunityLicence\ExpireAllForLicence::class,
        'LicenceVehicle\ClearSpecifiedDateAndInterimAppForLicence'
            => Query\LicenceVehicle\ClearSpecifiedDateAndInterimAppForLicence::class,
        'LicenceVehicle\RemoveAllForLicence' => Query\LicenceVehicle\RemoveAllForLicence::class,
        'Discs\CeaseDiscsForLicence' => Query\Discs\CeaseDiscsForLicence::class,
        'Discs\GoodsDiscsSetIsPrinting' => Query\Discs\GoodsDiscsSetIsPrinting::class,
        'Discs\GoodsDiscsSetIsPrintingOffAndDiscNo' => Query\Discs\GoodsDiscsSetIsPrintingOffAndDiscNo::class,
        'Discs\PsvDiscsSetIsPrinting' => Query\Discs\PsvDiscsSetIsPrinting::class,
        'Discs\PsvDiscsSetIsPrintingOffAndDiscNo' => Query\Discs\PsvDiscsSetIsPrintingOffAndDiscNo::class,
    ]
];
