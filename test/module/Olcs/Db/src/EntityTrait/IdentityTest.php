<?php

/**
 * Identity Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Identity Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class IdentityTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\Identity');
    }

    /**
     * Test setId
     *
     * @dataProvider providerSetId
     */
    public function testSetId($input, $output)
    {
        $this->entity->setId($input);

        $this->assertEquals($output, $this->entity->getId());
    }

    /**
     * Provider for setId
     */
    public function providerSetId()
    {
        return array(
            array(9, 9)
        );
    }
}
