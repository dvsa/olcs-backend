<?php


namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Data\Object\File as DsFile;
use Dvsa\Olcs\DocumentShare\Service\WebDavClient as Client;
use Hamcrest\Core\IsTypeOf;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Dvsa\Olcs\DocumentShare\Service\Client
 */
class WebDavClientTest extends MockeryTestCase
{
    const BASE_URI = 'http://testing';
    const WORKSPACE = 'unit_Workspace';

    /** @var  Client */
    protected $sut;

    /** @var  m\MockInterface | FilesystemInterface */
    private $mockFileSystem;

    /** @var  m\MockInterface|DsFile */
    private $mockFile;

    /** @var  m\MockInterface|\Zend\Log\Logger */
    private $logger;

    public function setUp()
    {
        $this->mockFileSystem = m::mock(FilesystemInterface::class);

        $this->sut = new Client($this->mockFileSystem);

        $this->mockFile = m::mock(DsFile::class);

        // Mock the logger
        $logWriter = m::mock(\Zend\Log\Writer\WriterInterface::class);

        $this->logger = m::mock(\Zend\Log\Logger::class, [])->makePartial();
        $this->logger->addWriter($logWriter);

        Logger::setLogger($this->logger);
    }

    public function testReadSuccess()
    {
        $expectContent = 'unit_ABCD1234';
        $testPath = 'test';
        $testStream = fopen('data://text/plain;base64,' . base64_encode($expectContent), 'r');

        $this->mockFileSystem->expects('readStream')->with($testPath)->andReturn($testStream);

        $actual = $this->sut->read($testPath);

        $this->assertInstanceOf(DsFile::class, $actual);
        $this->assertEquals($expectContent, file_get_contents($actual->getResource()));
    }


    public function testReadFail()
    {
        $testPath = 'test';

        $this->mockFileSystem->expects('readStream')->with($testPath)->andReturn(false);

        $actual = $this->sut->read($testPath);

        $this->assertEquals(false, $actual);
    }


    public function testReadFileNotFound()
    {
        $testPath = 'test';

        $this->mockFileSystem->expects('readStream')->with($testPath)->andThrow(new FileNotFoundException($testPath));

        $actual = $this->sut->read($testPath);
        $this->assertEquals(false, $actual);
    }

    public function testWriteSuccess()
    {
        $expectPath = 'unit_Path';
        $expectContent = 'unit_ABCDE123';

        $res = vfsStream::newFile('res')
            ->withContent($expectContent)
            ->at(vfsStream::setup('temp'))
            ->url();

        /** @var DsFile $mockFile */
        $mockFile = m::mock(DsFile::class)
            ->shouldReceive('getResource')->once()->andReturn($res)
            ->getMock();

        $this->mockFileSystem->expects('writeStream')->with($expectPath, new IsTypeOf('resource'))->andReturn(true);

        $actual = $this->sut->write($expectPath, $mockFile);

        static::assertEquals(true, $actual);
    }

    public function testWriteFail()
    {
        $expectPath = 'unit_Path';
        $expectContent = 'unit_ABCDE123';

        $res = vfsStream::newFile('res')
            ->withContent($expectContent)
            ->at(vfsStream::setup('temp'))
            ->url();

        /** @var DsFile $mockFile */
        $mockFile = m::mock(DsFile::class)
            ->shouldReceive('getResource')->once()->andReturn($res)
            ->getMock();

        $this->mockFileSystem->expects('writeStream')->with($expectPath, new IsTypeOf('resource'))->andReturn(false);

        $actual = $this->sut->write($expectPath, $mockFile);

        static::assertEquals(false, $actual);
    }

    public function testWriteFileAlreadyExists()
    {
        $expectPath = 'unit_Path';
        $expectContent = 'unit_ABCDE123';

        $res = vfsStream::newFile('res')
            ->withContent($expectContent)
            ->at(vfsStream::setup('temp'))
            ->url();

        /** @var DsFile $mockFile */
        $mockFile = m::mock(DsFile::class)
            ->shouldReceive('getResource')->once()->andReturn($res)
            ->getMock();

        $this->mockFileSystem->expects('writeStream')->with($expectPath, new IsTypeOf('resource'))->andThrow(
            new FileExistsException($expectPath)
        );

        $actual = $this->sut->write($expectPath, $mockFile);

        static::assertEquals(false, $actual);
    }

    public function testRemoveSuccess()
    {
        $this->mockFileSystem->expects('delete')->with('testFileToUnlink')->andReturn(true);

        $result = $this->sut->remove('testFileToUnlink');

        static::assertEquals(true, $result);
    }

    public function testRemoveFail()
    {
        $this->mockFileSystem->expects('delete')->with('testFileToUnlink')->andReturn(false);

        $result = $this->sut->remove('testFileToUnlink');

        static::assertEquals(false, $result);
    }

    public function testRemoveFileNotFound()
    {
        $this->mockFileSystem->expects('delete')->with('testFileToUnlink')->andThrow(
            new FileNotFoundException('test')
        );

        $result = $this->sut->remove('testFileToUnlink');

        static::assertEquals(false, $result);
    }
}
