<?php

/**
 * Date Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Date;
use OlcsTest\Bootstrap;

/**
 * Date Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new Date($this->sm);
    }

    public function testGetDateWithNoParams()
    {
        // as much as I don't like computed expectations in tests,
        // there's no real way round it here...
        $this->assertEquals(date('Y-m-d'), $this->sut->getDate());
    }

    public function testGetDateWithParams()
    {
        // as much as I don't like computed expectations in tests,
        // there's no real way round it here...
        $this->assertEquals(date('m-d'), $this->sut->getDate('m-d'));
    }

    public function testGetDateObject()
    {
        $this->assertInstanceOf('DateTime', $this->sut->getDateObject());
    }

    public function testGetDateObjectFromArray()
    {
        $obj = $this->sut->getDateObjectFromArray(
            [
                'day' => '07',
                'month' => '01',
                'year' => '2015'
            ]
        );

        $this->assertInstanceOf('DateTime', $obj);
        $this->assertEquals('2015-01-07', $obj->format('Y-m-d'));
    }

    public function testCalculateDate()
    {
        $date = new \DateTime('2015-01-01');

        $result = $this->sut->calculateDate($date, 10);

        $this->assertEquals('2015-01-11', $result->format('Y-m-d'));
    }
}
