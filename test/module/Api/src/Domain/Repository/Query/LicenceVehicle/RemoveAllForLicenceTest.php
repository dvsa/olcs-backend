<?php

/**
 * Cease Discs For Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\Repository\Query\LicenceVehicle\RemoveAllForLicence;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Cease Discs For Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RemoveAllForLicenceTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        LicenceVehicle::class => 'licence_vehicle'
    ];

    protected $columnNameMap = [
        LicenceVehicle::class => [
            'removalDate' => [
                'column' => 'removal_date'
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
                [
                    'removalDate' => $today->format('Y-m-d H:i:s')
                ]
            ]
        ];
    }

    protected function getSut()
    {
        return new RemoveAllForLicence();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE licence_vehicle lv SET lv.removal_date = :removalDate WHERE lv.licence_id = :licence';
    }
}
