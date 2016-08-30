<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\CaseworkerDetails;

/**
 * @covers Dvsa\Olcs\Api\Service\Document\Bookmark\CaseworkerDetails
 */
class CaseworkerDetailsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new CaseworkerDetails();
        $query = $bookmark->getQuery(['user' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithContactDetailsAddress()
    {
        $bookmark = new CaseworkerDetails();
        $bookmark->setData(
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
            ]
        );
        $this->assertEquals(
            "A User\nLine 1\nDirect Line: \ne-mail: a@user.com",
            $bookmark->render()
        );
    }

    public function testRenderWithContactDetailsAddressAndDirectDial()
    {
        $bookmark = new CaseworkerDetails();
        $bookmark->setData(
            [
                'contactDetails' => [
                    'emailAddress' => 'a@user.com',
                    'address' => [
                        'addressLine1' => 'Line 1'
                    ],
                    'phoneContacts' => [
                        [
                            'phoneContactType' => ['id' => 'phone_t_tel'],
                            'phoneNumber' => '0113 123 1234'
                        ]
                    ],
                    'person' => [
                        'forename' => 'A',
                        'familyName' => 'User',
                    ]
                ]
            ]
        );
        $this->assertEquals(
            "A User\nLine 1\nDirect Line: 0113 123 1234\ne-mail: a@user.com",
            $bookmark->render()
        );
    }

    public function testRenderWithTrafficAreaContactDetailsAddress()
    {
        $bookmark = new CaseworkerDetails();
        $bookmark->setData(
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
            ]
        );
        $this->assertEquals(
            "A User\nAn Area\nTA 11\nDirect Line: \ne-mail: a@user.com",
            $bookmark->render()
        );
    }
}
