<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Ebsr\EbsrProcessingChain;
use Dvsa\Olcs\Api\Service\Ebsr\EbsrProcessingChainFactory;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessor;
use Dvsa\Olcs\Api\Service\Ebsr\S3Processor;
use Dvsa\Olcs\Api\Service\Ebsr\ZipProcessor;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Laminas\Log\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Mockery as m;

class EbsrProcessingChainFactoryTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvoke(): void
    {
        $mockToggleService = m::mock(ToggleService::class);
        $mockToggleService->shouldReceive('isEnabled')->with(FeatureToggle::BACKEND_TRANSXCHANGE)->andReturn(true);
        $logger = new Logger();
        $logger->addWriter(new \Laminas\Log\Writer\Noop());

        $mockContainer = m::mock(ContainerInterface::class);
        $mockContainer->shouldReceive('get')->with(ToggleService::class)->andReturn($mockToggleService);
        $mockContainer->shouldReceive('get')->with(ZipProcessor::class)->andReturn(m::mock(ZipProcessor::class));
        $mockContainer->shouldReceive('get')->with(S3Processor::class)->andReturn(m::mock(S3Processor::class));
        $mockContainer->shouldReceive('get')->with('Logger')->andReturn($logger);
        $sut = new EbsrProcessingChainFactory();
        $service = $sut->__invoke($mockContainer, EbsrProcessingChain::class);

        $this->assertInstanceOf(EbsrProcessingChain::class, $service);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithToggleDisabled(): void
    {
        $mockToggleService = m::mock(ToggleService::class);
        $mockToggleService->shouldReceive('isEnabled')->with(FeatureToggle::BACKEND_TRANSXCHANGE)->andReturn(false);
        $mockContainer = m::mock(ContainerInterface::class);
        $mockContainer->shouldReceive('get')->with(ToggleService::class)->andReturn($mockToggleService);
        $mockContainer->shouldReceive('get')->with(FileProcessor::class)->andReturn(m::mock(FileProcessor::class));
        $mockContainer->shouldReceive('get')->with('Logger')->andReturn(m::mock(Logger::class));
        $sut = new EbsrProcessingChainFactory();
        $service = $sut->__invoke($mockContainer, EbsrProcessingChain::class);

        $this->assertInstanceOf(EbsrProcessingChain::class, $service);
    }
}
