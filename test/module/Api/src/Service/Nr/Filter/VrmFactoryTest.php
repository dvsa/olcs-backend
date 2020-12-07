<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Filter;

use Dvsa\Olcs\Transfer\Filter\Vrm as TransferVrmFilter;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Dvsa\Olcs\Api\Service\Nr\Filter\VrmFactory;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Nr\Filter\Vrm;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class VrmFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\Filter
 */
class VrmFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockTransferFilter = m::mock(TransferVrmFilter::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with(TransferVrmFilter::class)->andReturn($mockTransferFilter);

        $sut = new VrmFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(Vrm::class, $service);
        $this->assertSame($mockTransferFilter, $service->getVrmFilter());
    }
}
