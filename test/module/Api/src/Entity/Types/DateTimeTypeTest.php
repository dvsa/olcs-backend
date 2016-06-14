<?php

namespace Dvsa\OlcsTest\Api\Entity\Types;

use Dvsa\Olcs\Api\Entity\Types\DateTimeType;
use \Mockery as m;

/**
 * Class DateTimeTypeTest
 */
class DateTimeTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeType
     */
    private $sut;

    public function setup()
    {
        DateTimeType::overrideType('datetime', DateTimeType::class);
        $this->sut = DateTimeType::getType('datetime');
    }

    /**
     * @dataProvider dpConvertToPhpValue
     * @param $value
     * @param $expected
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function testConvertToPhpValue($value, $expected)
    {
        $mockPlatform = m::mock('\Doctrine\DBAL\Platforms\MySqlPlatform')->makePartial();
        $this->assertSame($expected, $this->sut->convertToPHPValue($value, $mockPlatform));
    }

    public function dpConvertToPhpValue()
    {
        return [
            [null, null],
            [new \DateTime('2016-06-03 15:43', new \DateTimeZone('UTC')), '2016-06-03T15:43:00+0000'],
            [new \DateTime('2016-06-03 15:43', new \DateTimeZone('Europe/London')), '2016-06-03T15:43:00+0100'],
            ['2016-06-03 13:03:55', '2016-06-03T13:03:55+0000'],
            ['2016-06-03 15:43', '2016-06-03T15:43:00+0000'],
            ['2016-12-25 15:43', '2016-12-25T15:43:00+0000'],
        ];
    }

    /**
     * @dataProvider dpConvertToDatabaseValue
     *
     * @param $value
     * @param $expected
     */
    public function testConvertToDatabaseValue($value, $expected)
    {
        $mockPlatform = m::mock('\Doctrine\DBAL\Platforms\MySqlPlatform')->makePartial();
        $this->assertSame($expected, $this->sut->convertToDatabaseValue($value, $mockPlatform));
    }

    public function dpConvertToDatabaseValue()
    {
        return [
            [null, null],
            [new \DateTime('2016-06-03 15:43', new \DateTimeZone('UTC')), '2016-06-03 15:43:00'],
            [new \DateTime('2016-06-03 15:43', new \DateTimeZone('Europe/London')), '2016-06-03 14:43:00'],
            ['2016-06-03 13:03:55+0100', '2016-06-03 12:03:55'],
            ['2016-06-03 13:03:55', '2016-06-03 13:03:55'],
        ];
    }
}
