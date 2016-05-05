<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\TaAddress;

/**
 * TA Address test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TaAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getQuery
     */
    public function testGetQuery()
    {
        $bookmark = new TaAddress();
        $queries = $bookmark->getQuery(['licence' => 123, 'user' => 345]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $queries[0]);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $queries[1]);
    }

    /**
     * Test render
     *
     * @dataProvider dataProvider
     * @param $queries
     * @param $expected
     */
    public function testRender($queries, $expected)
    {
        $bookmark = new TaAddress();
        $bookmark->setData($queries);

        $this->assertEquals(
            $expected,
            $bookmark->render()
        );
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            'user' => [
                [
                    [
                        'team' => [
                            'trafficArea' => [
                                'name' => 'TA Address 2'
                            ]
                        ],
                        'contactDetails' => [
                            'address' => [
                                'addressLine1' => 'Line 5',
                                'addressLine2' => 'Line 6',
                                'addressLine3' => 'Line 7',
                                'addressLine4' => 'Line 8',
                                'postcode' => 'LS1 4ES'
                            ]
                        ]
                    ],
                    [
                        'trafficArea' => [
                            'name' => 'TA Address 25'
                        ]
                    ]
                ],
                "TA Address 25\nLine 5\nLine 6\nLine 7\nLine 8\nLS1 4ES"
            ],
            'licence' => [
                [
                    [],
                    [
                        'trafficArea' => [
                            'name' => 'TA Address 1',
                            'contactDetails' => [
                                'address' => [
                                    'addressLine1' => 'Line 1',
                                    'addressLine2' => 'Line 2',
                                    'addressLine3' => 'Line 3',
                                    'addressLine4' => 'Line 4',
                                    'postcode' => 'LS2 4DD'
                                ]
                            ]
                        ]
                    ]
                ],
                "TA Address 1\nLine 1\nLine 2\nLine 3\nLine 4\nLS2 4DD"
            ],
            'empty' => [
                [
                    [],
                    [
                        'trafficArea' => [
                            'name' => 'TA Adress 3'
                        ]
                    ]
                ],
                "TA Adress 3"
            ]
        ];
    }
}
