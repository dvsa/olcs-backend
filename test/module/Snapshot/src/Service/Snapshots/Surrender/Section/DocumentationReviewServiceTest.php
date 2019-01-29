<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\DocumentationReviewService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class DocumentationReviewServiceTest extends MockeryTestCase
{
    /** @var DocumentationReviewService review service */
    protected $sut;

    public function setUp()
    {
        $this->sut = new DocumentationReviewService();
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

        $mockEntity->shouldReceive('getLicenceDocumentStatus->getDescription')->andReturn($args['licDocDescription']);
        $mockEntity->shouldReceive('getLicenceDocumentStatus->getId')->andReturn($args['licDocStatus']);
        $mockEntity->shouldReceive('getLicenceDocumentInfo')->andReturn($args['licDocInfo']);
        $mockEntity->shouldReceive('getLicence->getLicenceType->getId')->andReturn($args['licType']);
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
                    'licDocDescription' => 'Document lost',
                    'licDocStatus' => RefData::SURRENDER_DOC_STATUS_LOST,
                    'licDocInfo' => 'Document lost',
                    'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'commLicDescription' => 'Document stolen',
                    'commLicStatus' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                    'commLicInfo' => 'Document stolen',
                ],
                [

                    'multiItems' => [
                        [
                            [
                                'label' => 'surrender-review-documentation-operator-licence',
                                'value' => 'Document lost'
                            ],
                            [
                                'label' => 'surrender-review-additional-information',
                                'value' => 'Document lost'
                            ],
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
                    'licDocDescription' => 'Document destroyed',
                    'licDocStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                    'licDocInfo' => null,
                    'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'commLicDescription' => 'Document destroyed',
                    'commLicStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                    'commLicInfo' => null,
                ],
                [

                    'multiItems' => [
                        [
                            [
                                'label' => 'surrender-review-documentation-operator-licence',
                                'value' => 'Document destroyed'
                            ],
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
