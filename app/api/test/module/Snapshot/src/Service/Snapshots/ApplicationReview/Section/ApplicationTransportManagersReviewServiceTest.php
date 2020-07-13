<?php

/**
 * Application Transport Managers Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationTransportManagersReviewService;

/**
 * Application Transport Managers Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTransportManagersReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationTransportManagersReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = ['transportManagers' => ['bar' => 'foo']];

        $expected = [
            'subSections' => [
                [
                    'mainItems' => ['foo' => 'bar']
                ]
            ]
        ];

        $mockTm = m::mock();
        $this->sm->setService('Review\TransportManagers', $mockTm);

        $mockTm->shouldReceive('getConfigFromData')
            ->with(['bar' => 'foo'])
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
