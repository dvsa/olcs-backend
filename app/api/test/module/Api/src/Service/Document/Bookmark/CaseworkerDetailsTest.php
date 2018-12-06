<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Api\Service\Document\Bookmark\CaseworkerDetails;

/**
 * @covers \Dvsa\Olcs\Api\Service\Document\Bookmark\CaseworkerDetails
 */
class CaseworkerDetailsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new CaseworkerDetails();
        $query = $bookmark->getQuery(['user' => 123, 'licence' => 456]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query[0]);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query[1]);
        $this->assertCount(2, $query);
    }

    public function testGetQueryNoLicence()
    {
        $bookmark = new CaseworkerDetails();
        $query = $bookmark->getQuery(['user' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query[0]);
        $this->assertCount(1, $query);
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $expected)
    {
        $bookmark = new CaseworkerDetails();
        $bookmark->setData($data);

        $this->assertEquals($expected, $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            // testRenderWithContactDetailsAddress
            [
                [
                    [
                        'contactDetails' => [
                            'emailAddress' => 'a@user.com',
                            'address' => [
                                'addressLine1' => 'Line 1'
                            ],
                            'person' => [
                                'forename' => 'A',
                                'familyName' => 'User',
                            ]
                        ]
                    ],
                    []
                ],
                "A User\nLine 1\nDirect Line: \ne-mail: a@user.com"
            ],
            // testRenderWithContactDetailsAddressAndDirectDial
            [
                [
                    [
                        'contactDetails' => [
                            'emailAddress' => 'a@user.com',
                            'address' => [
                                'addressLine1' => 'Line 1'
                            ],
                            'phoneContacts' => [
                                [
                                    'phoneContactType' => ['id' => PhoneContact::TYPE_SECONDARY],
                                    'phoneNumber' => '0113 222 2222'
                                ],
                                [
                                    'phoneContactType' => ['id' => PhoneContact::TYPE_PRIMARY],
                                    'phoneNumber' => '0113 111 1111'
                                ]
                            ],
                            'person' => [
                                'forename' => 'A',
                                'familyName' => 'User',
                            ]
                        ]
                    ],
                    []
                ],
                "A User\nLine 1\nDirect Line: 0113 111 1111\ne-mail: a@user.com"
            ],
            // testRenderWithTrafficAreaContactDetailsAddress
            [
                [
                    [
                        'contactDetails' => [
                            'emailAddress' => 'a@user.com',
                            'address' => [],
                            'person' => [
                                'forename' => 'A',
                                'familyName' => 'User',
                            ],
                            'phoneContacts' => [
                                [
                                    'phoneContactType' => [
                                        'id' => 'INVALID_TYPE',
                                    ],
                                ]
                            ],
                        ],
                        'team' => [
                            'trafficArea' => [
                                'name' => 'An Area',
                                'contactDetails' => [
                                    'address' => [
                                        'addressLine1' => 'TA 11'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'trafficArea' => [
                            'name' => 'North East of England',
                            'isNi' => false,
                        ]
                    ]
                ],
                "A User\nOffice of the Traffic Commissioner\n"
                . "North East of England\nTA 11\nDirect Line: \ne-mail: a@user.com",
            ],
            // testRenderWithTrafficAreaContactDetailsAddress for NI
            [
                [
                    [
                        'contactDetails' => [
                            'emailAddress' => 'a@user.com',
                            'address' => [],
                            'person' => [
                                'forename' => 'A',
                                'familyName' => 'User',
                            ],
                            'phoneContacts' => [
                                [
                                    'phoneContactType' => [
                                        'id' => 'INVALID_TYPE',
                                    ],
                                ]
                            ],
                        ],
                        'team' => [
                            'trafficArea' => [
                                'name' => 'An Area',
                                'contactDetails' => [
                                    'address' => [
                                        'addressLine1' => 'TA 11'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'trafficArea' => [
                            'name' => 'Northern Ireland',
                            'isNi' => true,
                        ]
                    ]
                ],
                "A User\nNorthern Ireland\nTA 11\nDirect Line: \ne-mail: a@user.com",
            ],
            [
                [
                    [
                        'contactDetails' => [
                            'emailAddress' => 'a@user.com',
                            'address' => [],
                            'person' => [
                                'forename' => 'A',
                                'familyName' => 'User',
                            ],
                            'phoneContacts' => [
                                [
                                    'phoneContactType' => [
                                        'id' => 'INVALID_TYPE',
                                    ],
                                ]
                            ],
                        ],
                        'team' => [
                            'trafficArea' => [
                                'name' => 'An Area',
                                'contactDetails' => [
                                    'address' => [
                                        'addressLine1' => 'TA 11'
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                "A User\nTA 11\nDirect Line: \ne-mail: a@user.com",
            ],
        ];
    }
}
