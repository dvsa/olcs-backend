<?php

namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Data\Object\File as DsFile;
use Dvsa\Olcs\DocumentShare\Service\DocManClient;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_MockObject_MockObject as MockObj;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @covers \Dvsa\Olcs\DocumentShare\Service\DocManClient
 */
class DocManClientTest extends MockeryTestCase
{
    const BASE_URI = 'http://testing';
    const WORKSPACE = 'unit_Workspace';

    /** @var  DocManClient */
    protected $sut;

    /** @var  MockObj | \Zend\Http\Client */
    private $mockClient;
    /** @var  m\MockInterface|DsFile */
    private $mockFile;

    /** @var  m\MockInterface|\Zend\Log\Logger */
    private $logger;

    public function setUp()
    {
        $this->mockClient = $this->createMock(\Zend\Http\Client::class);

        $this->sut = new DocManClient(
            $this->mockClient,
            self::BASE_URI,
            self::WORKSPACE
        );
        $this->sut->setUuid('UUID1');

        $this->mockFile = m::mock(DsFile::class);

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
    }

    public function testReadOk()
    {
        $expectContent = 'unit_ABCD1234';
        $content = '{"content":"' . base64_encode($expectContent) . '"}';

        $mockResponse = m::mock(\Zend\Http\Response::class)
            ->shouldReceive('isSuccess')->once()->andReturn(true)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setMethod')->with(Request::METHOD_GET)->willReturnSelf();
        $this->mockClient
            ->expects(static::once())
            ->method('setUri')
            ->with(self::BASE_URI . '/content/' . self::WORKSPACE . '/test')
            ->willReturnSelf();
        $this->mockClient
            ->expects(static::once())
            ->method('setStream')
            ->with(static::stringContains('/' . DocManClient::DS_DOWNLOAD_FILE_PREFIX))
            ->willReturnCallback(
                function ($filePath) use ($content) {
                    file_put_contents($filePath, $content);

                    return $this->mockClient;
                }
            );
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        //  call & check
        $actual = $this->sut->read('test');

        static::assertInstanceOf(DsFile::class, $actual);
        static::assertEquals($expectContent, file_get_contents($actual->getResource()));
    }

    public function testReadExceptionUnlink()
    {
        $filePath = null;

        $this->mockClient->expects(static::once())->method('setRequest')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setUri')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setMethod')->willReturnSelf();
        $this->mockClient
            ->expects(static::once())
            ->method('setStream')
            ->willReturnCallback(
                function ($arg) use (&$filePath) {
                    $filePath = $arg;
                    return $this->mockClient;
                }
            );
        $this->mockClient->expects(static::once())->method('send')->willThrowException(new \Exception('simulate_err'));

        static::assertFalse(is_file($filePath));

        //  expect
        $this->expectException(\Exception::class, 'simulate_err');

        //  call & check
        $this->sut->read('test');
    }

    public function testReadNullNotSuccess()
    {
        $mockResponse = m::mock(\Zend\Http\Response::class)
            ->shouldReceive('isSuccess')->once()->andReturn(false)
            ->shouldReceive('getStatusCode')->once()->andReturn(600)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setUri')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setMethod')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setStream')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        $this->logger
            ->shouldReceive('log')
            ->once()
            ->with(\Zend\Log\Logger::ERR, DocManClient::ERR_RESP_FAIL, []);

        //  call & check
        $actual = $this->sut->read('test');

        static::assertEquals(null, $actual);
    }

    public function testReadNullProcessErr()
    {
        $content = '{"message":"unit_ErrMsg"}';

        $mockResponse = m::mock(\Zend\Http\Response::class)
            ->shouldReceive('isSuccess')->once()->andReturn(true)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setUri')->willReturnSelf();
        $this->mockClient->expects(static::once())->method('setMethod')->willReturnSelf();
        $this->mockClient
            ->expects(static::once())
            ->method('setStream')
            ->willReturnCallback(
                function ($filePath) use ($content) {
                    file_put_contents($filePath, $content);

                    return $this->mockClient;
                }
            );
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

        $res = vfsStream::newFile('res')
            ->withContent($expectContent)
            ->at(vfsStream::setup('temp'))
            ->url();

        /** @var DsFile $mockFile */
        $mockFile = m::mock(DsFile::class)
            ->shouldReceive('getMimeType')->once()->andReturn($expectMime)
            ->shouldReceive('getResource')->once()->andReturn($res)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->willReturnCallback(
            function (Request $request) {
                $expectJson = '{"hubPath":"unit_Path","mime":"unit_Mime","content":"dW5pdF9BQkNERTEyMw=="}';

                $this->assertSame(Request::METHOD_POST, $request->getMethod());

                $this->assertEquals(strlen($expectJson), $request->getHeader('Content-Length')->getFieldValue());
                $this->assertEquals('UUID1', $request->getHeader('uuid')->getFieldValue());
                $this->assertEquals($expectJson, $request->getContent());

                return $this->mockClient;
            }
        );
        $mockResponse = m::mock(Response::class)->shouldReceive('isOk')->andReturn(true);
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        $actual = $this->sut->write($expectPath, $mockFile);

        static::assertEquals('EXPECTED', $actual);
    }

    /**
     * @dataProvider dpRemove
     */
    public function testRemove($uri, $hard)
    {
        $this->mockClient->expects(static::once())->method('setRequest')->willReturnCallback(
            function (Request $request) use ($uri) {
                $this->assertSame($uri, $request->getUri()->toString());
                $this->assertSame(Request::METHOD_DELETE, $request->getMethod());

                return $this->mockClient;
            }
        );
        $mockResponse = m::mock(Response::class)->shouldReceive('isOk')->andReturn(true);
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        $result = $this->sut->remove('test', $hard);

        static::assertTrue($result);

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
