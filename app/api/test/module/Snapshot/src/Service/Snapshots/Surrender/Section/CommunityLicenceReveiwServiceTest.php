<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Surrender;
use \Mockery as m;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CommunityLicenceReviewService;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CommunityLicenceReveiwServiceTest extends MockeryTestCase
{
    /** @var CommunityLicenceReviewService review service */
    protected $sut;

    public function setUp(): void
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
        $mockEntity->shouldReceive('getCommunityLicenceDocumentStatus->getDescription')->once()->andReturn($args['commLicDescription']);
        $mockEntity->shouldReceive('getCommunityLicenceDocumentStatus->getId')->once()->andReturn($args['commLicStatus']);
        if ($this->dataName() !== 'community_licence_destroyed') {
            $mockEntity->shouldReceive('getCommunityLicenceDocumentInfo')->once()->andReturn($args['commLicInfo']);
        }
        $this->assertEquals($expected, $this->sut->getConfigFromData($mockEntity));
    }

    public function dpTestGetConfigFromData()
    {
        return [
            'stolen_community_licence' => [
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
            'community_licence_destroyed' => [
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
