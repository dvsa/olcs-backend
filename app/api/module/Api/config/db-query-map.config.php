<?php

use Dvsa\Olcs\Api\Domain\Repository\Query;

return [
    'factories' => [
        'ceaseDiscsForLicence' => Query\CeaseDiscsForLicence::class,
        'CommunityLicence\ExpireAllForLicence' => Query\CommunityLicence\ExpireAllForLicence::class,
        'LicenceVehicle\ClearSpecifiedDateAndInterimAppForLicence'
            => Query\LicenceVehicle\ClearSpecifiedDateAndInterimAppForLicence::class,
        'LicenceVehicle\RemoveAllForLicence' => Query\LicenceVehicle\RemoveAllForLicence::class,
    ]
];
