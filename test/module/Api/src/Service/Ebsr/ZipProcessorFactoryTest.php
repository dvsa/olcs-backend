<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Service\Ebsr\ZipProcessor;
use Dvsa\Olcs\Api\Service\Ebsr\ZipProcessorFactory;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Laminas\Filter\Decompress;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Laminas\Log\LoggerInterface;

class ZipProcessorFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $sut = new ZipProcessorFactory();
        $service = $sut->createService($this->mockContainer());
        $this->assertInstanceOf(ZipProcessor::class, $service);
    }

    private function mockContainer()
    {
        $mockContainer = m::mock(ServiceLocatorInterface::class);
        $mockContainer->shouldReceive('get')->with('Config')->andReturn(
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
