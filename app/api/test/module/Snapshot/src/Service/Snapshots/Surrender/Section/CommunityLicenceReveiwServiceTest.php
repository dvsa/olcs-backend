<?php


namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CommunityLicenceReviewService;

class CommunityLicenceReveiwServiceTest
{

    /** @var CommunityLicenceReviewService review service */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CommunityLicenceReviewService();
    }


    /**
     * @dataProvider dpTestGetConfigFromData
     *
     * @param $args
     */
    public function testGetConfigFromData(
        $args,
        $expected
    ) {
        $mockEntity = m::mock(Surrender::class);

        $mockEntity->shouldReceive('getCommunityLicenceDocumentStatus->getId')->andReturn($args['commLicStatus']);
        $mockEntity->shouldReceive('getCommunityLicenceDocumentStatus->getDescription')->andReturn($args['commLicDescription']);
        $mockEntity->shouldReceive('getCommunityLicenceDocumentInfo')->andReturn($args['commLicInfo']);

        $this->assertEquals($expected, $this->sut->getConfigFromData($mockEntity));
    }

    public function dpTestGetConfigFromData()
    {
        return [

            0 => [
                [
                      'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'commLicDescription' => 'Document stolen',
                    'commLicStatus' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                    'commLicInfo' => 'Document stolen',
                ],
                [

                    'multiItems' => [
                        [
                            [
                                'label' => 'surrender-review-documentation-community-licence',
                                'value' => 'Document stolen'
                            ],
                            [
                                'label' => 'surrender-review-additional-information',
                                'value' => 'Document stolen'
                            ]
                        ]
                    ]
                ]
            ],
            1 => [
                [
                    'licDocDescription' => 'Document destroyed',
                    'licDocStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                    'licDocInfo' => null,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    'commLicDescription' => null,
                    'commLicStatus' => null,
                    'commLicInfo' => null,

                ],
                [

                    'multiItems' => [
                        [
                            [
                                'label' => 'surrender-review-documentation-operator-licence',
                                'value' => 'Document destroyed'
                            ]
                        ]
                    ]
                ]
            ],
            2 => [
                [

                    'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'commLicDescription' => 'Document destroyed',
                    'commLicStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                    'commLicInfo' => null,
                ],
                [

                    'multiItems' => [
                        [

                            [
                                'label' => 'surrender-review-documentation-community-licence',
                                'value' => 'Document destroyed'
                            ],
                        ]
                    ]
                ]

            ]
        ];
    }
}
