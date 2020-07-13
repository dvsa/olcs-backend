<?php

/**
 * Application Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationOperatingCentresReviewService;

/**
 * Application Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentresReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationOperatingCentresReviewService();
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
                        'TACONFIG',
                        'TOTAL_AUTH_CONFIG'
                    ]
                ]
            ]
        ];

        // Mocks
        $mockOcService = m::mock();
        $mockTotalAuthService = m::mock();
        $mockTaService = m::mock();
        $this->sm->setService('Review\\' . $expectedOcService, $mockOcService);
        $this->sm->setService('Review\\' . $expectedTaService, $mockTotalAuthService);
        $this->sm->setService('Review\TrafficArea', $mockTaService);

        // Expectations
        $mockTaService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TACONFIG');

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
                ['foo' => 'bar'],
                ['foo' => 'cake']
            ]
        ];
        $expected = [
            'subSections' => [
                [
                    'mainItems' => [
                        'foobar',
                        'foocake'
                    ],
                ],
                [
                    'mainItems' => [
                        'TACONFIG',
                        'TOTAL_AUTH_CONFIG'
                    ]
                ]
            ]
        ];

        // Mocks
        $mockOcService = m::mock();
        $mockTotalAuthService = m::mock();
        $mockTaService = m::mock();
        $this->sm->setService('Review\\' . $expectedOcService, $mockOcService);
        $this->sm->setService('Review\\' . $expectedTaService, $mockTotalAuthService);
        $this->sm->setService('Review\TrafficArea', $mockTaService);

        // Expectations
        $mockTaService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TACONFIG');

        $mockTotalAuthService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TOTAL_AUTH_CONFIG');

        $mockOcService->shouldReceive('getConfigFromData')
            ->with(['foo' => 'bar'])
            ->andReturn('foobar')
            ->shouldReceive('getConfigFromData')
            ->with(['foo' => 'cake'])
            ->andReturn('foocake');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function psvProvider()
    {
        return [
            [
                true,
                'GoodsOperatingCentre',
                'ApplicationGoodsOcTotalAuth'
            ],
            [
                false,
                'PsvOperatingCentre',
                'ApplicationPsvOcTotalAuth'
            ]
        ];
    }
}
