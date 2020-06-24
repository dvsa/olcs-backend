<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory\RefundInterimFeesFactory;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\RefundInterimFees;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

class RefundInterimFeesFactoryTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sut = new RefundInterimFeesFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        $this->sm->shouldReceive('getServiceLocator')->andReturnSelf();

        // Mocks
        $this->sm->setService(
            'RepositoryServiceManager',
            m::mock()
                ->shouldReceive('get')
                ->once()
                ->with('Fee')
                ->andReturn(m::mock(Fee::class))
                ->getMock()
        );

        $refundInterimFees = $this->sut->createService($this->sm);

        $this->assertInstanceOf(RefundInterimFees::class, $refundInterimFees);
    }
}
