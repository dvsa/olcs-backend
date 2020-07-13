<?php

/**
 * Variation Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationOperatingCentresReviewService;

/**
 * Variation Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentresReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationOperatingCentresReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider psvProvider
     */
    public function testGetConfigFromDataWithEmptyOcList($isGoods, $expectedOcService, $expectedTaService)
    {
        $data = [
            'isGoods' => $isGoods,
            'operatingCentres' => []
        ];
        $expected = [
            'subSections' => [
                [
                    'mainItems' => [
                        'TOTAL_AUTH_CONFIG'
                    ]
                ]
            ]
        ];

        // Mocks
        $mockOcService = m::mock();
        $mockTotalAuthService = m::mock();
        $this->sm->setService('Review\\' . $expectedOcService, $mockOcService);
        $this->sm->setService('Review\\' . $expectedTaService, $mockTotalAuthService);

        // Expectations
        $mockTotalAuthService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TOTAL_AUTH_CONFIG');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    /**
     * @dataProvider psvProvider
     */
    public function testGetConfigFromDataWithOcList($isGoods, $expectedOcService, $expectedTaService)
    {
        $data = [
            'isGoods' => $isGoods,
            'operatingCentres' => [
                [
                    'action' => 'A',
                    'foo' => 'bar'
                ],
                [
                    'action' => 'A',
                    'foo' => 'bar1'
                ],
                [
                    'action' => 'U',
                    'foo' => 'cake'
                ],
                [
                    'action' => 'D',
                    'foo' => 'blah'
                ]
            ]
        ];
        $expected = [
            'subSections' => [
                [
                    'title' => 'variation-review-operating-centres-added-title',
                    'mainItems' => [
                        'foobar',
                        'foobar1'
                    ],
                ],
                [
                    'title' => 'variation-review-operating-centres-updated-title',
                    'mainItems' => [
                        'foocake'
                    ],
                ],
                [
                    'title' => 'variation-review-operating-centres-deleted-title',
                    'mainItems' => [
                        'fooblah'
                    ],
                ],
                [
                    'mainItems' => [
                        'TOTAL_AUTH_CONFIG'
                    ]
                ]
            ]
        ];

        // Mocks
        $mockOcService = m::mock();
        $mockTotalAuthService = m::mock();
        $this->sm->setService('Review\\' . $expectedOcService, $mockOcService);
        $this->sm->setService('Review\\' . $expectedTaService, $mockTotalAuthService);

        // Expectations
        $mockTotalAuthService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TOTAL_AUTH_CONFIG');

        $mockOcService->shouldReceive('getConfigFromData')
            ->with(['action' => 'A', 'foo' => 'bar'])
            ->andReturn('foobar')
            ->shouldReceive('getConfigFromData')
            ->with(['action' => 'A', 'foo' => 'bar1'])
            ->andReturn('foobar1')
            ->shouldReceive('getConfigFromData')
            ->with(['action' => 'U', 'foo' => 'cake'])
            ->andReturn('foocake')
            ->shouldReceive('getConfigFromData')
            ->with(['action' => 'D', 'foo' => 'blah'])
            ->andReturn('fooblah');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function psvProvider()
    {
        return [
            [
                true,
                'GoodsOperatingCentre',
                'VariationGoodsOcTotalAuth'
            ],
            [
                false,
                'PsvOperatingCentre',
                'VariationPsvOcTotalAuth'
            ]
        ];
    }
}
