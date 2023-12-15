<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Service\Ebsr\FileProcessor;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessorFactory;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Interop\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\Filter\Decompress;

/**
 * Class FileProcessorFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr
 */
class FileProcessorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $mockUploader = m::mock(FileUploaderInterface::class);

        $mockFilter = m::mock(Decompress::class);
        $mockFilter->shouldReceive('setAdapter')->with('zip');

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Decompress')->andReturn($mockFilter);

        $mockSl->shouldReceive('get')->with('FileUploader')->andReturn($mockUploader);

        $sut = new FileProcessorFactory();

        $this->assertInstanceOf(FileProcessor::class, $sut->__invoke($mockSl, FileProcessor::class));
    }
}
