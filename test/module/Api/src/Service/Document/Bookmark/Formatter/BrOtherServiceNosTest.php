<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark\Formatter;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter\BrOtherServiceNos;

/**
 * BrOtherServiceNos Test
 */
class BrOtherServiceNosTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dpTestFormat
     */
    public function testFormat($input, $expected)
    {
        $this->assertEquals($expected, BrOtherServiceNos::format($input));
    }

    /**
     * @return array
     */
    public function dpTestFormat()
    {
        return [
            [
                [],
                ''
            ],
            [
                [
                    0 => [
                        'serviceNo' => 3
                    ],
                    1 => [
                        'serviceNo' => 'abc'
                    ],
                    2 => [
                        'serviceNo' => '2'
                    ]
                ],
                '(3, abc, 2)'
            ],
        ];
    }
}
