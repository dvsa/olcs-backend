<?php

/**
 * DateClosed Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * DateClosed Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class DateClosedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Set Get Case
     */
    public function testSetGetCase()
    {
        $date = new \DateTime('NOW');

        $this->assertSame($date, $this->getNewSut()->setDateClosed($date)->getDateClosed());
    }

    /**
     * Gets an instance of the sut.
     *
     * @return \Olcs\Db\EntityTrait\DateClosed
     */
    public function getNewSut()
    {
        return $this->getMockForTrait('\Olcs\Db\EntityTrait\DateClosed');
    }
}
