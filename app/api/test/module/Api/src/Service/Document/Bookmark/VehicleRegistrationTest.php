<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\VehicleRegistration;

/**
 * Vehicle Registration Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleRegistrationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQuery()
    {
        $bookmark = new VehicleRegistration();
        $query = $bookmark->getQuery(['vehicle' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }


    public function testRender()
    {
        $bookmark = new VehicleRegistration();
        $bookmark->setData(
            [
                'vrm' => 'AB123'
            ]
        );

        $this->assertEquals(
            'AB123',
            $bookmark->render()
        );
    }
}
