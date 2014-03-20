<?php

/**
 * Tests User Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\User;

/**
 * Tests User Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->service = new User();
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array('username');

        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }
}
