<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Service\Ebsr\ZipProcessor;
use Dvsa\Olcs\Api\Service\Ebsr\ZipProcessorFactory;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Laminas\Filter\Decompress;
use Mockery as m;
use Laminas\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

class ZipProcessorFactoryTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testInvoke(): void
    {
        $sut = new ZipProcessorFactory();
        $service = $sut->__invoke($this->mockContainer(), ZipProcessor::class);
        $this->assertInstanceOf(ZipProcessor::class, $service);
    }

    private function mockContainer(): m\MockInterface
    {
        $mockContainer = m::mock(ContainerInterface::class);
        $mockContainer->shouldReceive('get')->with('config')->andReturn(
            [
                "tmpDirectory" => "test",
                "ebsr" => [
                    'tmp_extra_path' => 'unit_TmpDir',
                ],
            ]
        );
        $mockContainer->shouldReceive('get')->with('Logger')->andReturn(m::mock(LoggerInterface::class));
        $mockContainer->shouldReceive('get')->with('FileUploader')->andReturn(m::mock(FileUploaderInterface::class));
        $mockContainer->shouldReceive('get')->with('FilterManager')->andReturn(
            m::mock()->shouldReceive('get')->with('Decompress')->andReturn(
                m::mock(Decompress::class)->shouldReceive('setAdapter')->with('zip')->getMock()
            )->getMock()
        );

        return $mockContainer;
    }
}
