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
    ]
];
