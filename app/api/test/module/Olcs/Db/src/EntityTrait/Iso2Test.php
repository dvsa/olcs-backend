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
class Iso2Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\Iso2');
    }

    /**
     * Test setIso2
     *
     * @dataProvider providerSetHandle
     */
    public function testIso2($input, $output)
    {
        $this->entity->setIso2($input);

        $this->assertEquals($output, $this->entity->getIso2());
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
