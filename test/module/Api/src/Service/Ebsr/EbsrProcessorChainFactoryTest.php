<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Ebsr\EbsrProcessingChain;
use Dvsa\Olcs\Api\Service\Ebsr\EbsrProcessingChainFactory;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessor;
use Dvsa\Olcs\Api\Service\Ebsr\S3Processor;
use Dvsa\Olcs\Api\Service\Ebsr\ZipProcessor;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Laminas\Log\Logger;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Mockery as m;

class EbsrProcessorChainFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreateService()
    {
        $mockToggleService = m::mock(ToggleService::class);
        $mockToggleService->shouldReceive('isEnabled')->with(FeatureToggle::BACKEND_TRANSXCHANGE)->andReturn(true);
        $logger = m::mock(Logger::class);

        $logger->shouldReceive('log')->with(logger::INFO, 'TXC toggle on', []);
        $mockContainer = m::mock(ServiceLocatorInterface::class);
        $mockContainer->shouldReceive('get')->with(ToggleService::class)->andReturn($mockToggleService);
        $mockContainer->shouldReceive('get')->with(ZipProcessor::class)->andReturn(m::mock(ZipProcessor::class));
        $mockContainer->shouldReceive('get')->with(S3Processor::class)->andReturn(m::mock(S3Processor::class));
        $mockContainer->shouldReceive('get')->with('Logger')->andReturn($logger);
        $sut = new EbsrProcessingChainFactory();
        $service = $sut->createService($mockContainer);

        $this->assertInstanceOf(EbsrProcessingChain::class, $service);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreateServiceWithToggleDisabled() {
        $mockToggleService = m::mock(ToggleService::class);
    ;
        $mockToggleService->shouldReceive('isEnabled')->with(FeatureToggle::BACKEND_TRANSXCHANGE)->andReturn(false);

        $mockContainer = m::mock(ServiceLocatorInterface::class);
        $mockContainer->shouldReceive('get')->with(ToggleService::class)->andReturn($mockToggleService);
        $mockContainer->shouldReceive('get')->with(FileProcessor::class)->andReturn(m::mock(FileProcessor::class));
        $mockContainer->shouldReceive('get')->with('Logger')->andReturn(m::mock(Logger::class));
        $sut = new EbsrProcessingChainFactory();
        $service = $sut->createService($mockContainer);

        $this->assertInstanceOf(EbsrProcessingChain::class, $service);
    }


}