<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\Filter;

use Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectIsTxcApp;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class InjectIsTxcAppTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\Filter
 */
class InjectIsTxcAppTest extends TestCase
{
    public function testFilter()
    {
        $sut = new InjectIsTxcApp();
        $return = $sut->filter([]);

        $this->assertEquals('Y', $return['isTxcApp']);
    }

    /**
     * @dataProvider provideEbsrRefresh
     * @param $appType
     * @param $expected
     */
    public function testFilterInjectsEbsrRefresh($appType, $expected)
    {
        $sut = new InjectIsTxcApp();
        $return = $sut->filter(['txcAppType' => $appType]);

        $this->assertEquals($expected, $return['ebsrRefresh']);
    }

    public function provideEbsrRefresh()
    {
        return [
            ['nonChargeableChange', 'Y'],
            ['new', 'N'],
            ['cancel', 'N'],
            ['chargeableChange', 'N']
        ];
    }
}
