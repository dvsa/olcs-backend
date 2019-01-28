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


    public function testGetConfigFromData(
        $licDocDescription,
        $licDocStatus,
        $licDocInfo,
        $licenceType,
        $commLicDescription,
        $commLicStatus,
        $commLicInfo,
        $expected
    ) {
        $mockEntity = m::mock(Surrender::class);

        $mockEntity->shouldReceive('getLicenceDocumentStatus->getDescription')->andReturn($licDocDescription);
        $mockEntity->shouldReceive('getLicenceDocumentStatus->getId')->andReturn($licDocStatus);
        $mockEntity->shouldReceive('getLicenceDocumentInfo')->andReturn($licDocInfo);
        $mockEntity->shouldReceive('getLicence->getLicenceType->getId')->andReturn($licenceType);
        $mockEntity->shouldReceive('getCommunityLicenceDocumentStatus')->andReturn($commLicDescription);
        $mockEntity->shouldReceive('getCommunityLicenceDocumentStatus->getId')->andReturn($commLicStatus);
        $mockEntity->shouldReceive('getCommunityLicenceDocumentInfo')->andReturn($commLicInfo);

        $this->assertEquals($expected, $this->sut->getConfigFromData($mockEntity));
    }

    public function dpTestGetConfigFromData()
    {
        return [
            [
                'licDocDescription' => 'Document lost',
                'licDocStatus' => RefData::SURRENDER_DOC_STATUS_LOST,
                'licDocInfo' => 'got lost',
                'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'commLicDescription' => 'Document stolen',
                'commLicStatus' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                'commLicInfo' => 'Document stolen',
                'expectedResult' => [
                    'multiItems' => [
                        [
                            'label' => 'surrender-review-documentation-operator-licence',
                            'value' => 'Document lost'
                        ],
                        [
                            'label' => 'surrender-review-additional-information',
                            'value' => 'got lost'
                        ],
                        [
                            'label' => 'surrender-review-documentation-community-licence',
                            'value' => 'Document stolen'
                        ],
                        [
                            'label' => 'surrender-review-additional-information',
                            'value' => RefData::SURRENDER_DOC_STATUS_STOLEN
                        ]
                    ]
                ]
            ],
            [
                'licDocDescription' => 'Document destoryed',
                'licDocStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                'licDocInfo' => 'got lost',
                'licType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'commLicDescription' => null,
                'commLicStatus' => null,
                'commLicInfo' => null,
                'expectedResult' => [
                    'multiItems' => [
                        [
                            'label' => 'surrender-review-documentation-operator-licence',
                            'value' => 'Document lost'
                        ],
                        [
                            'label' => 'surrender-review-additional-information',
                            'value' => 'got lost'
                        ],
                    ]
                ]
            ],
            [
                'licDocDescription' => 'Document destroyed',
                'licDocStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                'licDocInfo' => null,
                'licType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'commLicDescription' => 'Document destroyed',
                'commLicStatus' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                'commLicInfo' => null,
                'expectedResult' => [
                    'multiItems' => [
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
        ];
    }
}