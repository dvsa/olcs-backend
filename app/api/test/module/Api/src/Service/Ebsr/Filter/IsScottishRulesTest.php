<?php


namespace Dvsa\OlcsTest\Api\Service\Ebsr\Filter;

use Dvsa\Olcs\Api\Service\Ebsr\Filter\IsScottishRules;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class IsScottishRulesTest
 * @package OlcsTest\Ebsr\Filter
 */
class IsScottishRulesTest extends TestCase
{
    /**
     * @dataProvider provideFilter
     * @param $data
     * @param $expected
     */
    public function testFilter($data, $expected)
    {
        $sut = new IsScottishRules();
        $result = $sut->filter($data);

        $this->assertEquals($expected, $result['busNoticePeriod']);
    }

    public function provideFilter()
    {
        return [
            [['trafficAreas' => ['English']], 2],
            [['trafficAreas' => ['Scottish']], 1],
            [['trafficAreas' => ['English', 'Scottish']], 1],
        ];
    }
}
