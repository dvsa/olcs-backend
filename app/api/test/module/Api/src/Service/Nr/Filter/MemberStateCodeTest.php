<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Filter;

use Dvsa\Olcs\Api\Service\Nr\Filter\MemberStateCode;

/**
 * @covers \Dvsa\Olcs\Api\Service\Nr\Filter\MemberStateCode
 */
class MemberStateCodeTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dpTestFilter
     */
    public function testFilter($value, $expect)
    {
        $value = [
            'memberStateCode' => $value,
        ];

        static::assertEquals(
            [
                'memberStateCode' => $expect,
            ],
            (new MemberStateCode())->filter($value)
        );
    }

    public function dpTestFilter()
    {
        return [
            [
                'value' => 'gb',
                'expect' => 'gb',
            ],
            ['uk', 'GB'],
            ['UK', 'GB'],
        ];
    }
}
