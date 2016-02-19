<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\Repository\Query\LicenceVehicle\ClearVehicleSection26;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;

/**
 * ClearVehicleSection26Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ClearVehicleSection26Test extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        Vehicle::class => 'vehicle',
        LicenceVehicle::class => 'licence_vehicle'
    ];

    protected $columnNameMap = [
        Vehicle::class => [
            'id' => [
                'column' => 'id'
            ],
            'section26' => [
                'column' => 'section_26'
            ],
            'lastModifiedOn' => [
                'column' => 'last_modified_on'
            ],
        ],
        LicenceVehicle::class => [
            'licence' => [
                'isAssocation' => true,
                'column' => 'licence_id'
            ],
            'removalDate' => [
                'column' => 'removal_date'
            ],
            'vehicle' => [
                'isAssocation' => true,
                'column' => 'vehicle_id'
            ],
        ]
    ];

    public function paramProvider()
    {
        return [
            [
                [
                    'licence' => 1702,
                ],
                [],
                [
                    'licence' => 1702,
                ],
                []
            ]
        ];
    }

    protected function getSut()
    {
        return new ClearVehicleSection26();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE vehicle v
      INNER JOIN licence_vehicle lv ON lv.vehicle_id = v.id
      SET v.section_26 = 0, v.last_modified_on = NOW()
      WHERE lv.licence_id = :licence
      AND v.section_26 <> 0';
    }
}
