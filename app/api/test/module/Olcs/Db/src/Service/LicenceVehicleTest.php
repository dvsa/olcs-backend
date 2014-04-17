<?php

/**
 * Tests LicenceVehicle Service
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\LicenceVehicle;

/**
 * Tests LicenceVehicle Service
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */
class LicenceVehicleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->service = new LicenceVehicle();
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array('licence', 'vehicle');
        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }
    
}
