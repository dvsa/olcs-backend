<?php

/**
 * Variation Convictions Penalties Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationConvictionsPenaltiesReviewService;

/**
 * Variation Convictions Penalties Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConvictionsPenaltiesReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sut = new VariationConvictionsPenaltiesReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [
            'foo' => 'bar'
        ];

        $mockApplicationService = m::mock();
        $this->sm->setService('Review\ApplicationConvictionsPenalties', $mockApplicationService);
        $mockApplicationService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('CONFIG');

        $this->assertEquals('CONFIG', $this->sut->getConfigFromData($data));
    }
}
