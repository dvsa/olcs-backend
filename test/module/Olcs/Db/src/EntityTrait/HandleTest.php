<?php

/**
 * Handle Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Handle Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class HandleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\Handle');
    }

    /**
     * Test setHandle
     *
     * @dataProvider providerSetHandle
     */
    public function testSetHandle($input, $output)
    {
        $this->entity->setHandle($input);

        $this->assertEquals($output, $this->entity->getHandle());
    }

    /**
     * Provider for setHandle
     */
    public function providerSetHandle()
    {
        return array(
            array('ABC', 'ABC')
        );
    }
}
