<?php

use Dvsa\Olcs\Api\Domain\Repository\Query;

return [
    'factories' => [
        'ceaseDiscsForLicence' => Query\CeaseDiscsForLicence::class,
        'clearLicenceVehicleSpecifiedDateAndInterimApp' => Query\ClearLicenceVehicleSpecifiedDateAndInterimApp::class,
        'CommunityLicence\ExpireAllForLicence' => Query\CommunityLicence\ExpireAllForLicence::class,
    ]
];
