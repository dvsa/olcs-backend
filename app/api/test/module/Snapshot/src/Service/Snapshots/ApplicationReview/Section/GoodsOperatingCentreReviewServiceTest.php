<?php

/**
 * Goods Operating Centre Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\GoodsOperatingCentreReviewService;

/**
 * Goods Operating Centre Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsOperatingCentreReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
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
            'sufficientParking' => 'Y',
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
                        'label' => 'review-operating-centre-sufficient-parking',
                        'value' => 'Confirmed'
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
                        'label' => 'review-operating-centre-sufficient-parking',
                        'value' => 'Confirmed'
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
                ->andReturn('no-files-uploaded-translated');
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
                'N',
                [],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-ad-placed',
                        'value' => 'No'
                    ]
                ],
                false
            ],
            [
                'Y',
                [
                    // This file should be ignored, as the app id doesn't match
                    [
                        'filename' => 'somefile.pdf',
                        'application' => [
                            'id' => 321
                        ]
                    ],
                    // These 2 should be included
                    [
                        'filename' => 'file1.pdf',
                        'application' => [
                            'id' => 123
                        ]
                    ],
                    [
                        'filename' => 'file2.pdf',
                        'application' => [
                            'id' => 123
                        ]
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-ad-placed',
                        'value' => 'Yes'
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
                false
            ],
            [
                'Y',
                [],
                [
                    [
                        'label' => 'review-operating-centre-advertisement-ad-placed',
                        'value' => 'Yes'
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
