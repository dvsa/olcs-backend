<?php

/**
 * Tests Person Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\Person;

/**
 * Tests Person Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PersonTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->service = new Person();
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array(
            'firstName',
            'surname'
        );

        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }
}
