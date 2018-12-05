<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\Filter\Format;

use Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\Subsidy;

/**
 * Class SubsidyTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\Filter\Format
 */
class SubsidyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideFilter
     * @param $expected
     * @param $value
     */
    public function testFilter($expected, $value)
    {
        $sut = new Subsidy();

        $result = $sut->filter(['subsidised' => $value]);
        $this->assertEquals($expected, $result['subsidised']);
    }

    public function provideFilter()
    {
        return [
            ['bs_no', 'none'],
            ['bs_yes', 'full'],
            ['bs_in_part', 'partial'],
            ['bs_no', null]
        ];
    }
}
