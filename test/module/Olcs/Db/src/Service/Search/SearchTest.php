<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\Search as SearchService;
use Mockery as m;
use PHPUnit_Framework_TestCase as TestCase;
use Elastica\Query as Query;

/**
 * Class Search Test
 *
 * @package OlcsTest\Db\Service\Search
 */
class SearchTest extends TestCase
{
    public function testProcessDateRanges()
    {
        $bool = new Query\Bool();

        $service = $this->getMock(SearchService::class, null);
        $service->setDateRanges(
            [
                'dateOneFrom' => ['year' => '2015', 'month' => '01', 'day' => '02'],
                'dateOneTo'   => '2015-03-01',
                'dateTwoFrom' => '2015-02-01',
                'dateTwoTo'   => '2015-04-01'
            ]
        );

        $result = array (
            'bool' => array (
                'must' => array (
                    0 => array (
                        'range' => array (
                            'date_one' => array (
                                'from' => '2015-01-02',
                                'to' => '2015-03-01',
                            ),
                        ),
                    ),
                    1 => array (
                        'range' => array (
                            'date_two' => array (
                                'from' => '2015-02-01',
                                'to' => '2015-04-01',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSame($result, $service->processDateRanges($bool)->toArray());
    }

    /**
     * Tests the filter methods and functionality.
     *
     * @dataProvider filterFunctionalityDataProvider
     *
     * @param array $setFilters
     * @param array $getFilters
     * @param array $filterNames
     *
     * @return void
     */
    public function testFilterFunctionality(array $setFilters, array $getFilters, array $filterNames)
    {
        $systemUnderTest = new SearchService();

        // Uses fluent interface to test
        $this->assertEquals($getFilters, $systemUnderTest->setFilters($setFilters)->getFilters());

        //die(var_export($systemUnderTest->getFilterNames(), 1));

        $this->assertEquals($filterNames, $systemUnderTest->getFilterNames());
    }

    /**
     * Data provider for testFilterFunctionality test.
     *
     * @return array
     */
    public function filterFunctionalityDataProvider()
    {
        return array(
            array(
                array( // set filters
                    'organisationName' => 'a',
                    'licenceTrafficArea' => 'b'
                ),
                array( // get filters
                    'organisation_name' => 'a',
                    'licence_traffic_area' => 'b'
                ),
                array( // names
                    'organisation_name',
                    'licence_traffic_area'
                )
            ),
        );
    }
}
