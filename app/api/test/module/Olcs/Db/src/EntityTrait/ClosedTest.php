<?php

/**
 * Closed Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Closed Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class ClosedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Set Get Case
     */
    public function testSetGetCase()
    {
        $sut = $this->getNewSut();

        $this->assertNull($sut->getDateClosed());

        $this->assertSame($sut, $sut->setClosed());

        $this->assertInstanceOf('\DateTime', $sut->getDateClosed());
    }

    /**
     * Gets an instance of the sut.
     *
     * @return \Olcs\Db\EntityTrait\Closed
     */
    public function getNewSut()
    {
        return $this->getMockForTrait('\Olcs\Db\EntityTrait\Closed');
    }
}
