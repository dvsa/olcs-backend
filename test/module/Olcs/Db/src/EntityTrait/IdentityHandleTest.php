<?php

/**
 * Handle Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Handle Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class IdentityHandleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\IdentityHandle');
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
