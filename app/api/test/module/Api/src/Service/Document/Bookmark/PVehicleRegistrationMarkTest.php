<?php

/**
 * PVehicleRegistrationMark Test
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\PVehicleRegistrationMark;

/**
 * PVehicleRegistrationMark Test
 */
class PVehicleRegistrationMarkTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQuery()
    {
        $bookmark = new PVehicleRegistrationMark();
        $query = $bookmark->getQuery(['impounding' => 123]);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
        $this->assertTrue(is_null($bookmark->getQuery([])));
    }

    public function testRender()
    {
        $bookmark = new PVehicleRegistrationMark();
        $bookmark->setData(['vrm' => 'AB12 CDE']);

        $this->assertEquals('AB12 CDE', $bookmark->render());
    }
}
