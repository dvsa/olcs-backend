<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact as PhoneContactEntity;
use Dvsa\Olcs\Api\Service\Document\Bookmark\BkmFax as Sut;

/**
 * BkmFax bookmark test
 */
class BkmFaxTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new Sut();
        $query = $bookmark->getQuery(['organisation' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $expected)
    {
        $bookmark = new Sut();
        $bookmark->setData($data);

        $this->assertEquals($expected, $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            [
                [
                    'irfoContactDetails' => [
                        'phoneContacts' => [
                            [
                                'phoneContactType' => [
                                    'id' => PhoneContactEntity::TYPE_BUSINESS
                                ],
                                'phoneNumber' => 'tel no'
                            ],
                            [
                                'phoneContactType' => [
                                    'id' => PhoneContactEntity::TYPE_HOME
                                ],
                                'phoneNumber' => 'home no'
                            ],
                            [
                                'phoneContactType' => [
                                    'id' => PhoneContactEntity::TYPE_MOBILE
                                ],
                                'phoneNumber' => 'mobile no'
                            ],
                            [
                                'phoneContactType' => [
                                    'id' => PhoneContactEntity::TYPE_FAX
                                ],
                                'phoneNumber' => 'fax no'
                            ],
                        ]
                    ]
                ],
                'fax no'
            ],
            [
                [
                    'irfoContactDetails' => [
                        'phoneContacts' => [
                            [
                                'phoneContactType' => [
                                    'id' => PhoneContactEntity::TYPE_MOBILE
                                ],
                                'phoneNumber' => 'mobile no'
                            ],
                        ]
                    ]
                ],
                ''
            ],
            [
                [],
                ''
            ],
        ];
    }
}
