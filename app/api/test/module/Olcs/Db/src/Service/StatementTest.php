<?php

/**
 * Tests Statement Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\Statement;

/**
 * Tests Statement Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StatementTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->service = new Statement();
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array();

        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }
}
