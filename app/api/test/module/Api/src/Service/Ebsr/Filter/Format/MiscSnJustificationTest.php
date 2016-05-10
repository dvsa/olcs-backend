<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\Filter\Format;

use Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\MiscSnJustification;

/**
 * Class MiscSnJustificationTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\Filter\Format
 * @covers Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\MiscSnJustification
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class MiscSnJustificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFilter
     * @param array $expected
     * @param array $value
     */
    public function testFilter($expected, $value)
    {
        $sut = new MiscSnJustification();

        $result = $sut->filter(['busShortNotice' => $value]);
        $this->assertEquals($expected, $result['busShortNotice']);
    }

    /**
     * Data provider for testFilter
     *
     * @return array
     */
    public function provideFilter()
    {
        $unforseenDetailValue = 'unforseen detail text';
        $unforseenDetailKey = 'unforseenDetail';
        $miscJustificationValue = 'misc justification text';
        $miscJustificationKey = 'miscJustification';
        $formattedMiscJustification = 'Miscellaneous justification: ' . $miscJustificationValue;

        $onlyUnforseen = [$unforseenDetailKey => $unforseenDetailValue];

        $bothHaveValuesInput = [
            $unforseenDetailKey => $unforseenDetailValue,
            $miscJustificationKey => $miscJustificationValue
        ];

        $bothHaveValuesResult = [
            $unforseenDetailKey => $unforseenDetailValue . ' ' . $formattedMiscJustification,
            $miscJustificationKey => $miscJustificationValue
        ];

        $onlyMiscJustificationInput = [
            $miscJustificationKey => $miscJustificationValue
        ];

        $onlyMiscJustificationResult = [
            $unforseenDetailKey => $formattedMiscJustification,
            $miscJustificationKey => $miscJustificationValue
        ];

        return [
            [$onlyUnforseen, $onlyUnforseen],
            [$bothHaveValuesResult, $bothHaveValuesInput],
            [$onlyMiscJustificationResult, $onlyMiscJustificationInput]
        ];
    }
}
