<?php

/**
 * PresidingTc Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * PresidingTc Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PresidingTcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Set Get Pi
     */
    public function testSetGetCase()
    {
        $presidingTc = new \Olcs\Db\Entity\PresidingTc();

        $this->assertSame($presidingTc, $this->getNewSut()->setPresidingTc($presidingTc)->getPresidingTc());
    }

    /**
     * Gets an instance of the sut.
     *
     * @return \Olcs\Db\EntityTrait\PresidingTc
     */
    public function getNewSut()
    {
        return $this->getMockForTrait('\Olcs\Db\EntityTrait\PresidingTc');
    }
}
