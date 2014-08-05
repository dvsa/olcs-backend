<?php

/**
 * Pi Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Pi Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Set Get Pi
     */
    public function testSetGetCase()
    {
        $pi = new \Olcs\Db\Entity\Pi();

        $this->assertSame($pi, $this->getNewSut()->setPi($pi)->getPi());
    }

    /**
     * Gets an instance of the sut.
     *
     * @return \Olcs\Db\EntityTrait\Pi
     */
    public function getNewSut()
    {
        return $this->getMockForTrait('\Olcs\Db\EntityTrait\Pi');
    }
}
