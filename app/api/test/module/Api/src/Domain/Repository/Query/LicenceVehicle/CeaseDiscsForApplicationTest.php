<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\Repository\Query\LicenceVehicle\CeaseDiscsForApplication;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;

/**
 * Cease Discs For Application Test
 */
class CeaseDiscsForApplicationTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        GoodsDisc::class => 'goods_disc',
        LicenceVehicle::class => 'licence_vehicle'
    ];

    protected $columnNameMap = [
        GoodsDisc::class => [
            'licenceVehicle' => [
                'isAssociation' => true,
                'column' => 'licence_vehicle_id'
            ],
            'ceasedDate' => [
                'column' => 'ceased_date'
            ],
            'isInterim' => [
                'column' => 'is_interim'
            ],
            'lastModifiedOn' => [
                'column' => 'last_modified_on'
            ],
            'lastModifiedBy' => [
                'column' => 'last_modified_by'
            ],
        ],
        LicenceVehicle::class => [
            'id' => [
                'column' => 'id'
            ],
            'application' => [
                'isAssocation' => true,
                'column' => 'application_id'
            ],
            'removalDate' => [
                'column' => 'removal_date'
            ],
            'specifiedDate' => [
                'column' => 'specified_date'
            ],
        ]
    ];

    public function paramProvider()
    {
        $today = new DateTime();

        return [
            [
                [],
                [],
                [
                    'ceasedDate' => $today->format('Y-m-d H:i:s')
                ],
                []
            ]
        ];
    }

    protected function getSut()
    {
        return new CeaseDiscsForApplication();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE goods_disc gd '
        . 'INNER JOIN licence_vehicle lv '
            . 'ON lv.id = gd.licence_vehicle_id '
        . 'SET gd.ceased_date = :ceasedDate, '
            . 'gd.is_interim = 0, '
            . 'gd.last_modified_on = NOW(), '
            . 'gd.last_modified_by = :currentUserId '
        . 'WHERE lv.application_id = :application '
            . 'AND lv.removal_date IS NULL '
            . 'AND lv.specified_date IS NOT NULL '
            . 'AND gd.ceased_date IS NULL';
    }
}
