<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\QueryTemplate;
use Mockery as m;

/**
 * Class QueryTemplateTest
 */
class QueryTemplateTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testQueryTemplateMissing()
    {
        $this->expectException(\RuntimeException::class);
        $sut = new QueryTemplate('foo.json', 'bar');
        // prevent unused variable violation
        unset($sut);
    }

    /**
     * @dataProvider queryTemplateDataProvider
     */
    public function testQueryTemplate($query, $filters, $filterTypes, $dates, $expected)
    {
        $sut = new QueryTemplate(__DIR__ . '/mock-query-template.json', $query, $filters, $filterTypes, $dates);
        $this->assertEquals($expected, $sut->getParam('query'));
    }

    public function queryTemplateDataProvider()
    {
        return [
            // simple query
            [
                'SMITH',
                [],
                [],
                null,
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SMITH'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query with special chars
            [
                'SM"\das\'[]{}ITH',
                [],
                [],
                null,
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SM"\das\'[]{}ITH'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query empty
            [
                '',
                [],
                [],
                null,
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => ''
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query single " (double quote)
            [
                '"',
                [],
                [],
                null,
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => '"'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query double " (double quote)
            [
                '""',
                [],
                [],
                null,
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => '""'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query ending with " (double quote)
            [
                '"SMITH"',
                [],
                [],
                null,
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => '"SMITH"'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query json
            [
                '{"key":"value"}',
                [],
                [],
                null,
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => '{"key":"value"}'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query with one filter
            [
                'SMITH',
                [
                    'field_1' => 'value1',
                ],
                [
                    'field_1' => 'DYNAMIC',
                ],
                [],
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SMITH'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'field_1' => 'value1'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query with multiple filters
            [
                'SMITH',
                [
                    'field_1' => 'value1',
                    'field_2' => 'value2',
                    'field_3' => '0',
                ],
                [
                    'field_1' => 'DYNAMIC',
                    'field_2' => 'DYNAMIC',
                    'field_3' => 'BOOLEAN',
                ],
                [],
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SMITH'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'field_1' => 'value1'
                                ]
                            ],
                            [
                                'term' => [
                                    'field_2' => 'value2'
                                ]
                            ],
                        ],
                        'must_not' => [
                            0 => [
                                'exists' => [
                                    'field' => 'field_3',
                                ]
                            ],
                        ],
                    ]
                ]
            ],
            // query with from_and_to date range
            [
                'SMITH',
                [],
                [],
                [
                    'field_1_from_and_to' => '2010-09-30'
                ],
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SMITH'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'field_1' => '2010-09-30'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query with from only date range
            [
                'SMITH',
                [],
                [],
                [
                    'field_1_from' => '2010-09-30'
                ],
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SMITH'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'field_1' => ['from' => '2010-09-30']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query with from and to date range
            [
                'SMITH',
                [],
                [],
                [
                    'field_1_from' => '2010-09-30',
                    'field_1_to' => '2010-10-30'
                ],
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SMITH'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'field_1' => ['from' => '2010-09-30', 'to' => '2010-10-30']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // query with filters and date ranges
            [
                'SMITH',
                [
                    'field_1' => 'value1',
                    'field_2' => 'value2',
                    'field_3' => 'value3',
                ],
                [
                    'field_1' => 'DYNAMIC',
                    'field_2' => 'DYNAMIC',
                    'field_3' => 'DYNAMIC',
                ],
                [
                    'field_1_from_and_to' => '2010-09-30',
                    'field_2_from' => '2010-09-30',
                    'field_3_from' => '2010-09-30',
                    'field_3_to' => '2010-10-30'
                ],
                [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'field_1' => 'SMITH'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'field_1' => 'value1'
                                ]
                            ],
                            [
                                'term' => [
                                    'field_2' => 'value2'
                                ]
                            ],
                            [
                                'term' => [
                                    'field_3' => 'value3'
                                ]
                            ],
                            [
                                'term' => [
                                    'field_1' => '2010-09-30'
                                ]
                            ],
                            [
                                'range' => [
                                    'field_2' => ['from' => '2010-09-30']
                                ]
                            ],
                            [
                                'range' => [
                                    'field_3' => ['from' => '2010-09-30', 'to' => '2010-10-30']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
