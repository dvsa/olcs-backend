<?php

/**
 * Description Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Description Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DescriptionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\Description');
    }

    /**
     * Test setDescription
     *
     * @dataProvider providerSetDescription
     */
    public function testSetDescription($input, $output)
    {
        $this->entity->setDescription($input);

        $this->assertEquals($output, $this->entity->getDescription());
    }

    /**
     * Provider for setDescription
     */
    public function providerSetDescription()
    {
        return array(
            array('ABC', 'ABC')
        );
    }
}
