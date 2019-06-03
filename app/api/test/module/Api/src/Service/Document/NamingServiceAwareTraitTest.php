<?php

namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Service\Document\NamingService;
use Dvsa\OlcsTest\Api\Service\Document\Stub\NamingServiceAwareTraitStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Dvsa\Olcs\Api\Service\Document\NamingServiceAwareTrait
 */
class NamingServiceAwareTraitTest extends MockeryTestCase
{
    public function testSetGet()
    {
        /** @var NamingService $mockNameSrv */
        $mockNameSrv = m::mock(NamingService::class);

        $sut = new NamingServiceAwareTraitStub();
        $sut->setNamingService($mockNameSrv);

        static::assertSame($mockNameSrv, $sut->getNamingService());
    }

    /**
     * @dataProvider dpTestDetermineEntityFromCommand
     */
    public function testDetermineEntityFromCommand($data, $expectArgs)
    {
        $mockRepo = m::mock(RepositoryInterface::class)
            ->shouldReceive('getReference')->once()->withArgs($expectArgs)->andReturn('EXPECTED')
            ->getMock();

        /** @var NamingServiceAwareTraitStub|m\MockInterface $sut */
        $sut = m::mock(NamingServiceAwareTraitStub::class . '[getRepo]')
            ->shouldReceive('getRepo')->atMost(1)->andReturn($mockRepo)
            ->getMock();

        static::assertEquals('EXPECTED', $sut->determineEntityFromCommand($data));
    }

    public function dpTestDetermineEntityFromCommand()
    {
        return [
            [
                'data' => ['case' => 9999],
                'expectArgs' => [Entity\Cases\Cases::class, 9999],
            ],
            [
                'data' => ['application' => 9998],
                'expectArgs' => [Entity\Application\Application::class, 9998],
            ],
            [
                'data' => ['transportManager' => 9997],
                'expectArgs' => [Entity\Tm\TransportManager::class, 9997],
            ],
            [
                'data' => ['busReg' => 9996],
                'expectArgs' => [Entity\Bus\BusReg::class, 9996],
            ],
            [
                'data' => ['licence' => 9995],
                'expectArgs' => [Entity\Licence\Licence::class, 9995],
            ],
            [
                'data' => ['irfoOrganisation' => 9994],
                'expectArgs' => [Entity\Organisation\Organisation::class, 9994],
            ],
            [
                'data' => ['irhpApplication' => 9993],
                'expectArgs' => [Entity\Permits\IrhpApplication::class, 9993],
            ],
        ];
    }

    public function testDetermineEntityFromCommandNull()
    {
        $sut = new NamingServiceAwareTraitStub;

        static::assertNull($sut->determineEntityFromCommand([]));
    }
}
