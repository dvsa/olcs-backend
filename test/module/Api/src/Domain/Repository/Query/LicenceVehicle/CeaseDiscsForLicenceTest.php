<?php

/**
 * Cease Discs For Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\Repository\Query\LicenceVehicle\CeaseDiscsForLicence;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Cease Discs For Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CeaseDiscsForLicenceTest extends AbstractDbQueryTestCase
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
        ],
        LicenceVehicle::class => [
            'id' => [
                'column' => 'id'
            ],
            'licence' => [
                'isAssocation' => true,
                'column' => 'licence_id'
            ]
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
        return new CeaseDiscsForLicence();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE goods_disc gd
      INNER JOIN licence_vehicle lv ON lv.id = gd.licence_vehicle_id
      SET gd.ceased_date = :ceasedDate, gd.is_interim = 0, gd.last_modified_on = NOW()
      WHERE lv.licence_id = :licence AND gd.ceased_date IS NULL';
    }
}
