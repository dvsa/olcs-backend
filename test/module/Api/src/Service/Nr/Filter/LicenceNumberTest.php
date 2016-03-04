<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Filter;

use Dvsa\Olcs\Api\Service\Nr\Filter\LicenceNumber;

/**
 * Class LicenceNumberTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\Filter
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceNumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test filter()
     */
    public function testFilter()
    {
        $initialValue = 'UKGB/OB1234567/00000';
        $expectedResult = 'OB1234567';
        $value = ['communityLicenceNumber' => $initialValue];
        $expected = [
            'communityLicenceNumber' => $initialValue,
            'licenceNumber' => $expectedResult
        ];

        $sut = new LicenceNumber();

        $this->assertEquals($expected, $sut->filter($value));
    }

    /**
     * @param $invalidValue
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\Exception
     *
     * @dataProvider exceptionProvider
     */
    public function testExceptions($invalidValue)
    {
        $value = ['communityLicenceNumber' => $invalidValue];
        $sut = new LicenceNumber();
        $sut->filter($value);
    }

    /**
     * data provider for testExceptions()
     *
     * @return array
     */
    public function exceptionProvider()
    {
        return [
            ['UKGB/OB1234567'],
            ['UKGB/OB1234567/00000/1111'],
            ['OB1234567'],
            ['UKGB-OB1234567-00000']
        ];
    }
}
