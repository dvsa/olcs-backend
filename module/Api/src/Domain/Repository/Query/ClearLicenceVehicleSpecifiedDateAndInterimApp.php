<?php

/**
 * Clear Licence Vehicle Specified Date And Interim App
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository\Query;

use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;

/**
 * Clear Licence Vehicle Specified Date And Interim App
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ClearLicenceVehicleSpecifiedDateAndInterimApp extends AbstractRawQuery
{
    protected $templateMap = [
        'lv' => LicenceVehicle::class
    ];

    protected $queryTemplate = 'UPDATE {lv}
      SET {lv.specifiedDate} = null, {lv.interimApplication} = null
      WHERE {lv.licence} = :licence';
}
