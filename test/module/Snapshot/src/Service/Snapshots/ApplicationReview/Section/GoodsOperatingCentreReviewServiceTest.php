<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\GoodsOperatingCentreReviewService;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;

/**
 * Goods Operating Centre Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsOperatingCentreReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sut = new GoodsOperatingCentreReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($withAd, $adDocuments, $expectedAdvertisements, $needToMockTranslator)
    {
        $data = [
            'id' => 321,
            'adPlaced' => $withAd,
            'adPlacedIn' => 'Some paper',
            'adPlacedDate' => '2014-03-02',
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 20,
            'permission' => 'N',
            'application' => [
                'id' => 123
            ],
            'operatingCentre' => [
                'adDocuments' => $adDocuments,
                'address' => [
                    'addressLine1' => 'Some building',
                    'addressLine2' => 'Foo street',
                    'town' => 'Bartown',
                    'postcode' => 'FB1 1FB'
                ]
            ]
        ];

        $psvConfig = [
            'header' => 'Some building, Bartown',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centre-address',
                        'value' => 'Some building, Foo street, Bartown, FB1 1FB'
                    ]
                ],
                'vehicles+trailers' => [
                    [
                        'label' => 'review-operating-centre-total-vehicles',
                        'value' => 10
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-permission',
                        'value' => 'Unconfirmed'
                    ]
                ]
            ]
        ];

        $expected = [
            'header' => 'Some building, Bartown',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centre-address',
                        'value' => 'Some building, Foo street, Bartown, FB1 1FB'
                    ]
                ],
                'vehicles+trailers' => [
                    [
                        'label' => 'review-operating-centre-total-vehicles',
                        'value' => 10
                    ],
                    [
                        'label' => 'review-operating-centre-total-trailers',
                        'value' => 20
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-permission',
                        'value' => 'Unconfirmed'
                    ]
                ],
                'advertisements' => $expectedAdvertisements
            ]
        ];

        // Mocks
        $mockPsvService = m::mock();
        $this->sm->setService('Review\PsvOperatingCentre', $mockPsvService);

        if ($needToMockTranslator) {
            $mockTranslator = m::mock();
            $this->sm->setService('translator', $mockTranslator);

            $mockTranslator->shouldReceive('translate')
                ->with('no-files-uploaded', 'snapshot')
                ->andReturn('no-files-uploaded-translated')
                ->shouldReceive('translate')
                ->with($expectedAdvertisements[0]['value'], 'snapshot')
                ->andReturn($expectedAdvertisements[0]['value']);
        }

        // Expectations
        $mockPsvService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn($psvConfig);

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                ApplicationOperatingCentre::AD_POST,
                [],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-ad-placed',
                        'value' => 'review-operating-centre-advertisement-post'
                    ]
                ],
                true
            ],
            [
                ApplicationOperatingCentre::AD_UPLOAD_LATER,
                [],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-ad-placed',
                        'value' => 'review-operating-centre-advertisement-upload-later'
                    ]
                ],
                true
            ],
            [
                ApplicationOperatingCentre::AD_UPLOAD_NOW,
                [
                    // This file should be ignored, as the app id doesn't match
                    [
                        'description' => 'somefile.pdf',
                        'application' => [
                            'id' => 321
                        ]
                    ],
                    // These 2 should be included
                    [
                        'description' => 'file1.pdf',
                        'application' => [
                            'id' => 123
                        ]
                    ],
                    [
                        'description' => 'file2.pdf',
                        'application' => [
                            'id' => 123
                        ]
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-ad-placed',
                        'value' => 'review-operating-centre-advertisement-upload-now'
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-newspaper',
                        'value' => 'Some paper'
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-date',
                        'value' => '02 Mar 2014'
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-file',
                        'noEscape' => true,
                        'value' => 'file1.pdf<br>file2.pdf'
                    ]
                ],
                true
            ],
            [
                ApplicationOperatingCentre::AD_UPLOAD_NOW,
                [],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-ad-placed',
                        'value' => 'review-operating-centre-advertisement-upload-now'
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-newspaper',
                        'value' => 'Some paper'
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-date',
                        'value' => '02 Mar 2014'
                    ],
                    [
                        'label' => 'review-operating-centre-advertisement-file',
                        'noEscape' => true,
                        'value' => 'no-files-uploaded-translated'
                    ]
                ],
                true
            ]
        ];
    }
}
