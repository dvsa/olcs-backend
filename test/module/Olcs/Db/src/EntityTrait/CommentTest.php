<?php

/**
 * Comment Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Comment Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = $this->getMockForTrait('\Olcs\Db\EntityTrait\Comment');
    }

    /**
     * Test setComment
     *
     * @dataProvider providerSetComment
     */
    public function testSetComment($input, $output)
    {
        $this->entity->setComment($input);

        $this->assertEquals($output, $this->entity->getComment());
    }

    /**
     * Provider for setComment
     */
    public function providerSetComment()
    {
        return array(
            array('ABC', 'ABC')
        );
    }
}
