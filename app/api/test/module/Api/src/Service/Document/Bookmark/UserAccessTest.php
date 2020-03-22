<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\UserAccess;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class UserAccessTest extends MockeryTestCase
{
    public function testGetQuery()
    {
        $bookmark = new UserAccess();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(QueryInterface::class, $query);
    }

    public function testRenderWithNoUsers()
    {
        $bookmark = new UserAccess();
        $bookmark->setData([]);

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithGoodsVehiclesSpecified()
    {
        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->andReturnUsing(function ($text) {
                return $text . '_translated';
            })
            ->getMock();

        $bookmark = m::mock(UserAccess::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSnippet')
            ->with('CHECKLIST_3CELL_TABLE')
            ->andReturn('snippet')
            ->once()
            ->shouldReceive('getTranslator')
            ->andReturn($mockTranslator)
            ->getMock();

        $bookmark->setData(
            [
                'organisation' => [
                    'organisationUsers' => [
                        [
                            'user' => [
                                'contactDetails' =>
                                    [
                                        'emailAddress' => 'test2@test.com',
                                        'person' =>
                                            [
                                                'familyName' => 'Test2',
                                                'forename' => 'Test2',
                                            ],
                                    ],
                                'roles' =>
                                    [
                                        [
                                            'role' => 'operator',
                                        ],
                                    ],
                            ]
                        ],
                        [
                            'user' => [
                                'contactDetails' =>
                                    [
                                        'emailAddress' => 'test1@test.com',
                                        'person' =>
                                            [
                                                'familyName' => 'Test1',
                                                'forename' => 'Test1',
                                            ],
                                    ],
                                'roles' =>
                                    [
                                        [
                                            'role' => 'admin',
                                        ],
                                    ],
                            ]
                        ],
                        [
                            'user' => [
                                'contactDetails' =>
                                    [
                                        'emailAddress' => 'test3@test.com',
                                        'person' =>
                                            [
                                                'familyName' => 'Test3',
                                                'forename' => 'Test3',
                                            ],
                                    ],
                                'roles' =>
                                    [
                                        [
                                            'role' => 'admin',
                                        ],
                                    ],
                            ]
                        ],
                        [
                            'user' => [
                                'contactDetails' =>
                                    [
                                        'emailAddress' => 'test33@test.com',
                                        'person' =>
                                            [
                                                'familyName' => 'Test3',
                                                'forename' => 'Test3',
                                            ],
                                    ],
                                'roles' =>
                                    [
                                        [
                                            'role' => 'admin',
                                        ],
                                    ],
                            ]
                        ]
                    ]
                ]
            ]
        );

        $header = [
            'BOOKMARK1' => 'Name',
            'BOOKMARK2' => 'Email address',
            'BOOKMARK3' => 'Permission'
        ];
        $row1 = [
            'BOOKMARK1' => 'Test1 Test1',
            'BOOKMARK2' => 'test1@test.com',
            'BOOKMARK3' => 'role.admin_translated'
        ];
        $row2 = [
            'BOOKMARK1' => 'Test2 Test2',
            'BOOKMARK2' => 'test2@test.com',
            'BOOKMARK3' => 'role.operator_translated'
        ];
        $row3 = [
            'BOOKMARK1' => 'Test3 Test3',
            'BOOKMARK2' => 'test3@test.com',
            'BOOKMARK3' => 'role.admin_translated'
        ];
        $row4 = [
            'BOOKMARK1' => 'Test3 Test3',
            'BOOKMARK2' => 'test33@test.com',
            'BOOKMARK3' => 'role.admin_translated'
        ];

        $emptyRow = [
            'BOOKMARK1' => '',
            'BOOKMARK2' => '',
            'BOOKMARK3' => ''
        ];

        $mockParser = m::mock('Dvsa\Olcs\Api\Service\Document\Parser\RtfParser')
            ->shouldReceive('replace')
            ->with('snippet', $header)
            ->andReturn('header|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row1)
            ->andReturn('row1|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row2)
            ->andReturn('row2|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row3)
            ->andReturn('row3|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row4)
            ->andReturn('row4|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $emptyRow)
            ->andReturn('emptyrow|')
            ->times(11)
            ->getMock();

        $bookmark->setParser($mockParser);

        $rendered = 'header|row1|row2|row3|row4|' . str_repeat('emptyrow|', 11);
        $this->assertEquals(
            $rendered,
            $bookmark->render()
        );
    }
}
