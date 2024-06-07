<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Domain\Exception\EbsrPackException;
use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\Api\Service\Ebsr\ZipProcessor;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Laminas\Filter\Decompress;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Psr\Log\LoggerInterface;

class ZipProcessorTest extends TestCase
{
    protected string $subDirPath;
    protected string $xmlFilename;
    protected string $extractDir;
    protected string $tmpEbsrFile;

    protected string $tmpDir;
    protected string $fileContent;
    protected string $fileIdentifier;
    protected LoggerInterface $mockLogger;

    public function setUp(): void
    {
        vfsStream::setup();

        $this->fileIdentifier = 'ebsr.zip';
        $this->fileContent = 'contents';
        $this->tmpDir = '/tmp';
        $this->tmpEbsrFile = 'ebsr123';
        $this->extractDir = vfsStream::url('root');
        $this->xmlFilename = vfsStream::url('root/ebsr.xml');
        $this->mockLogger = m::mock(LoggerInterface::class);
        $this->subDirPath = '/extra/path';
        parent::setUp();
    }
    public function testSubDir()
    {
        $sut = new ZipProcessor(
            m::mock(FileUploaderInterface::class),
            m::mock(Filesystem::class),
            m::mock(Decompress::class),
            'unit_TmpDir',
            $this->mockLogger,
            m::mock(\Symfony\Component\Finder\Finder::class)
        );
        $sut->setSubDirPath('unit_SubDirPath');
        $this->assertEquals('unit_SubDirPath', $sut->getSubDirPath());
    }

    public function testOutputType()
    {
        $sut = new ZipProcessor(
            m::mock(FileUploaderInterface::class),
            m::mock(Filesystem::class),
            m::mock(Decompress::class),
            'unit_TmpDir',
            $this->mockLogger,
            m::mock(\Symfony\Component\Finder\Finder::class)
        );
        $this->assertEquals('xmlFilename', $sut->getOutputType());
    }

    /**
     * @throws EbsrPackException
     */
    public function testProcess()
    {
        // test process of zip file
        $file = new vfsStreamFile($this->xmlFilename);
        $file->setContent('<xml</xml>');

        vfsStream::setup('root', null, ['ebsr.xml' => $file]);

        touch($this->xmlFilename);

        $mockFile = m::mock(\File::class);
        $mockFile->shouldReceive('getContent')->andReturn($this->fileContent);

        $mockFileUploader = m::mock(FileUploaderInterface::class);
        $mockFileUploader->shouldReceive('download')->with($this->fileIdentifier)->andReturn($mockFile);
        $mockFileUploader->shouldReceive('upload')->withAnyArgs()->andReturn(
            m::mock(File::class)->shouldReceive('getIdentifier')->andReturn($this->xmlFilename)->getMock()
        );

        $mockFileSystem = m::mock(Filesystem::class);
        $mockFileSystem->shouldReceive('exists')->with($this->tmpDir . $this->subDirPath)->andReturn(true);
        $mockFileSystem->shouldReceive('createTmpFile')->with($this->tmpDir . $this->subDirPath, 'zip')->andReturn($this->tmpEbsrFile);
        $mockFileSystem->shouldReceive('dumpFile')->with($this->tmpEbsrFile, $this->fileContent);
        $mockFileSystem->shouldReceive('createTmpDir')->with($this->tmpDir . $this->subDirPath, 'ebsr')->andReturn($this->extractDir);
        $mockFilter = m::mock(Decompress::class);
        $mockFilter->shouldReceive('setTarget')->with($this->extractDir);
        $mockFilter->shouldReceive('filter')->with($this->tmpEbsrFile);
        $mockFinder = m::mock(\Symfony\Component\Finder\Finder::class);
        $mockFinder->shouldReceive('files->name->in')->with($this->extractDir)->andReturn(
            $mockFinder->shouldReceive('getIterator')->andReturn(
                new \ArrayIterator(
                    [
                        $this->xmlFilename => $this->xmlFilename
                    ]
                )
            )->getMock()
        );
        $this->mockLogger->shouldReceive('debug')->with('Storing transxchange xml file in content store', ['tmpfile' => $this->xmlFilename]);
        $sut = new ZipProcessor($mockFileUploader, $mockFileSystem, $mockFilter, $this->tmpDir, $this->mockLogger, $mockFinder);
        $sut->setSubDirPath($this->subDirPath);

        $this->assertEquals(
            $this->xmlFilename,
            str_replace("\\", "/", $sut->process($this->fileIdentifier))
        );
    }
}
