<?php

/**
 * Name Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Name Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NameTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\Name');
    }

    /**
     * Test setName
     *
     * @dataProvider providerSetName
     */
    public function testSetName($input, $output)
    {
        $this->entity->setName($input);

        $this->assertEquals($output, $this->entity->getName());
    }

    /**
     * Provider for setName
     */
    public function providerSetName()
    {
        return array(
            array('ABC', 'ABC')
        );
    }
}
