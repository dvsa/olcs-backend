<?php

/**
 * SoftDelete Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * SoftDelete Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SoftDeleteTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\SoftDelete');
    }

    /**
     * Test Soft Delete
     *
     * @dataProvider providerSoftDelete
     */
    public function testSoftDelete($input, $output, $isDeleted)
    {
        $this->entity->setIsDeleted($input);

        $this->assertEquals($output, $this->entity->getIsDeleted());

        $this->assertSame($isDeleted, $this->entity->isDeleted());
    }

    /**
     * Provider for testSoftDelete
     */
    public function providerSoftDelete()
    {
        return array(
            array(0, 0, false),
            array(1, 1, true)
        );
    }
}
