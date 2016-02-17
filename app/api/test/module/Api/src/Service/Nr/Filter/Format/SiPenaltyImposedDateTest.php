<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Filter\Format;

use Dvsa\Olcs\Api\Service\Nr\Filter\Format\SiPenaltyImposedDate;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class SiPenaltyImposedDateTest
 * @package Dvsa\OlcsTest\Api\Service\NrFilter\Format
 */
class SiPenaltyImposedDateTest extends TestCase
{

    /**
     * Tests the filter
     *
     * @dataProvider filterProvider
     * @param $inputDates
     * @param $expectedDates
     */
    public function testFilter($inputDates, $expectedDates)
    {
        $input = ['imposedErrus' => [0 => $inputDates]];
        $expectedOutput = ['imposedErrus' => [0 =>$expectedDates]];

        $sut = new SiPenaltyImposedDate();
        $this->assertEquals($expectedOutput, $sut->filter($input));
    }

    /**
     * data provider for testFilterProvider
     */
    public function filterProvider()
    {
        return [
            [
                ['finalDecisionDate' => '2015-12-25'],
                [
                    'finalDecisionDate' => new \DateTime('2015-12-25 00:00:00'),
                    'startDate' => null,
                    'endDate' => null
                ]
            ],
            [
                [
                    'finalDecisionDate' => '2015-12-25',
                    'startDate' => '2015-12-26',
                    'endDate' => '2015-12-27'
                ],
                [
                    'finalDecisionDate' => new \DateTime('2015-12-25 00:00:00'),
                    'startDate' => new \DateTime('2015-12-26 00:00:00'),
                    'endDate' => new \DateTime('2015-12-27 00:00:00')
                ]
            ],
        ];
    }
}
