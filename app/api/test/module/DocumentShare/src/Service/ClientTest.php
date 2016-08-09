<?php

namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\DocumentShare\Data\Object\File as DocShareFile;
use Dvsa\Olcs\DocumentShare\Service\Client;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_MockObject_MockObject as MockObj;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @covers Dvsa\Olcs\DocumentShare\Service\Client
 */
class ClientTest extends MockeryTestCase
{
    const BASE_URI = 'http://testing';
    const WORKSPACE = 'unit_Workspace';

    /** @var  Client */
    protected $sut;

    /** @var  MockObj | \Zend\Http\Client */
    private $mockClient;
    /** @var  MockObj | \Zend\Http\Request */
    private $mockRequest;
    /** @var  m\MockInterface | Filesystem */
    private $mockFs;
    /** @var  m\MockInterface|File */
    private $mockFile;

    /** @var  m\MockInterface|\Zend\Log\Logger */
    private $logger;

    public function setUp()
    {
        $this->mockClient = $this->getMock(\Zend\Http\Client::class);
        $this->mockRequest = $this->getMock(\Zend\Http\Request::class);
        $this->mockFs = m::mock(Filesystem::class);

        $this->sut = new Client(
            $this->mockClient,
            $this->mockRequest,
            $this->mockFs,
            self::BASE_URI,
            self::WORKSPACE
        );

        $this->mockFile = m::mock(File::class);

        // Mock the logger
        $logWriter = m::mock(\Zend\Log\Writer\WriterInterface::class);

        $this->logger = m::mock(\Zend\Log\Logger::class, [])->makePartial();
        $this->logger->addWriter($logWriter);

        Logger::setLogger($this->logger);
    }

    public function testGet()
    {
        static::assertEquals(self::BASE_URI, $this->sut->getBaseUri());
        static::assertEquals(self::WORKSPACE, $this->sut->getWorkspace());
        static::assertEquals($this->mockRequest, $this->sut->getRequestTemplate());
    }

    public function testReadOk()
    {
        $expectContent = 'unit_ABCD1234';
        $content = '{"content": "' . base64_encode($expectContent) . '"}';

        //  mock config
        $mockFilePath = vfsStream::newFile('unit.xxx')
            ->setContent($content)
            ->at(vfsStream::setup('temp'))
            ->url();

        $this->mockFs->shouldReceive('createTmpFile')
            ->once()
            ->with(sys_get_temp_dir(), 'download')
            ->andReturn($mockFilePath);

        $mockResponse = m::mock(\Zend\Http\Response::class)
            ->shouldReceive('isSuccess')->once()->andReturn(true)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->with($this->mockRequest)->willReturnSelf();
        $this->mockClient
            ->expects(static::once())
            ->method('setMethod')->with(Request::METHOD_GET)
            ->willReturnSelf();
        $this->mockClient
            ->expects(static::once())
            ->method('setUri')
            ->with(self::BASE_URI . '/content/' . self::WORKSPACE . '/test')
            ->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setStream')->with($mockFilePath)->willReturnSelf();
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        //  call & check
        $actual = $this->sut->read('test');

        static::assertInstanceOf(DocShareFile::class, $actual);
        static::assertEquals($expectContent, file_get_contents($actual->getResource()));

        //  second call
        static::assertSame($actual, $this->sut->read('test'));
    }

    public function testReadNullNotSuccess()
    {
        $this->mockFs->shouldReceive('createTmpFile');

        $mockResponse = m::mock(\Zend\Http\Response::class)
            ->shouldReceive('isSuccess')->once()->andReturn(false)
            ->shouldReceive('getStatusCode')->once()->andReturn(600)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->with($this->mockRequest)->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setUri')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setMethod')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setStream')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        $this->logger
            ->shouldReceive('log')
            ->once()
            ->with(\Zend\Log\Logger::ERR, Client::ERR_RESP_FAIL, []);

        //  call & check
        $actual = $this->sut->read('test');

        static::assertEquals(null, $actual);
    }

    public function testReadNull()
    {
        //  mock config
        $mockFilePath = vfsStream::newFile('unit.xxx')
            ->setContent('{"message": "unit_ErrMsg"}')
            ->at(vfsStream::setup('temp'))
            ->url();

        $this->mockFs->shouldReceive('createTmpFile')
            ->once()
            ->andReturn($mockFilePath);

        $mockResponse = m::mock(\Zend\Http\Response::class)
            ->shouldReceive('isSuccess')->once()->andReturn(true)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->with($this->mockRequest)->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setUri')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setMethod')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setStream')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        $this->logger
            ->shouldReceive('log')
            ->once()
            ->with(\Zend\Log\Logger::INFO, 'unit_ErrMsg', []);

        //  call & check
        $actual = $this->sut->read('test');

        static::assertEquals(null, $actual);
    }

    public function testWrite()
    {
        $expectPath = 'unit_Path';
        $expectMime = 'unit_Mime';
        $expectContent = 'unit_ABCDE123';

        /** @var DocShareFile $mockFile */
        $mockFile = m::mock(DocShareFile::class)
            ->shouldReceive('getMimeType')->once()->andReturn($expectMime)
            ->shouldReceive('getContent')->once()->andReturn($expectContent)
            ->getMock();

        $expectJson = '{"hubPath": "unit_Path","mime": "unit_Mime","content": "dW5pdF9BQkNERTEyMw=="}';

        $mockHeaders = m::mock(Headers::class)
            ->makePartial()
            ->shouldReceive('addHeaderLine')->once()->with('Content-Length', strlen($expectJson))->andReturnSelf()
            ->shouldReceive('addHeaderLine')->once()->with('Content-Type', 'application/json')
            ->getMock();

        $this->mockRequest->expects(static::once())
            ->method('setUri')
            ->with(self::BASE_URI . '/content/' . self::WORKSPACE)
            ->willReturnSelf();

        $this->mockRequest->expects(static::once())->method('setMethod')->with(Request::METHOD_POST)->willReturnSelf();
        $this->mockRequest->expects(static::once())->method('setContent')->with($expectJson)->willReturnSelf();
        $this->mockRequest->expects(static::once())->method('getHeaders')->willReturn($mockHeaders);

        $this->mockClient->expects(static::once())->method('setRequest')->with($this->mockRequest)->willReturnSelf();
        $this->mockClient->expects(static::once())->method('send')->willReturn('EXPECTED');

        $actual = $this->sut->write($expectPath, $mockFile);

        static::assertEquals('EXPECTED', $actual);
    }

    /**
     * @dataProvider dpRemove
     */
    public function testRemove($uri, $hard)
    {
        $this->mockRequest->expects(static::once())->method('setUri')->with($uri)->willReturnSelf();
        $this->mockRequest->expects(static::once())->method('setMethod')->with(Request::METHOD_DELETE);

        $this->mockClient->expects(static::once())->method('setRequest')->with($this->mockRequest)->willReturnSelf();
        $this->mockClient->expects(static::once())->method('send')->willReturn('EXPECTED');

        $result = $this->sut->remove('test', $hard);

        static::assertEquals('EXPECTED', $result);
    }

    public function dpRemove()
    {
        return [
            [
                'uri' => 'http://testing/content/' . self::WORKSPACE . '/test',
                'hard' => false,
            ],
            [
                'uri' => 'http://testing/version/content/' . self::WORKSPACE . '/test',
                'hard' => true,
            ],
        ];
    }
}
