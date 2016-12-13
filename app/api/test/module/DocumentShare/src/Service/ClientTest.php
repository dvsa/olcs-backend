<?php

namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\DocumentShare\Data\Object\File as DocShareFile;
use Dvsa\Olcs\DocumentShare\Service\Client;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObj;
use Zend\Http\Request;

/**
 * @covers \Dvsa\Olcs\DocumentShare\Service\Client
 */
class ClientTest extends MockeryTestCase
{
    const BASE_URI = 'http://testing';
    const WORKSPACE = 'unit_Workspace';

    /** @var  Client */
    protected $sut;

    /** @var  MockObj | \Zend\Http\Client */
    private $mockClient;
    /** @var  m\MockInterface|File */
    private $mockFile;

    /** @var  m\MockInterface|\Zend\Log\Logger */
    private $logger;

    public function setUp()
    {
        $this->mockClient = $this->getMock(\Zend\Http\Client::class);

        $this->sut = new Client(
            $this->mockClient,
            self::BASE_URI,
            self::WORKSPACE
        );
        $this->sut->setUuid('UUID1');

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
            ->with(static::stringContains('/' . Client::DS_DOWNLOAD_FILE_PREFIX))
            ->willReturnCallback(
                function ($filePath) use ($content) {
                    file_put_contents($filePath, $content);

                    return $this->mockClient;
                }
            );
        $this->mockClient->expects(static::once())->method('send')->willReturn($mockResponse);

        //  call & check
        $actual = $this->sut->read('test');

        static::assertInstanceOf(DocShareFile::class, $actual);
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
        $this->setExpectedException(\Exception::class, 'simulate_err');

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
            ->with(\Zend\Log\Logger::ERR, Client::ERR_RESP_FAIL, []);

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

        /** @var DocShareFile $mockFile */
        $mockFile = m::mock(DocShareFile::class)
            ->shouldReceive('getMimeType')->once()->andReturn($expectMime)
            ->shouldReceive('getContent')->once()->andReturn($expectContent)
            ->getMock();

        $this->mockClient->expects(static::once())->method('setRequest')->willReturnCallback(
            function (Request $request) {
                $expectJson = '{"hubPath": "unit_Path","mime": "unit_Mime","content": "dW5pdF9BQkNERTEyMw=="}';

                $this->assertSame(Request::METHOD_POST, $request->getMethod());
                $this->assertEquals(strlen($expectJson), $request->getHeader('Content-Length')->getFieldValue());
                $this->assertEquals('UUID1', $request->getHeader('uuid')->getFieldValue());
                $this->assertEquals($expectJson, $request->getContent());

                return $this->mockClient;
            }
        );
        $this->mockClient->expects(static::once())->method('send')->willReturn('EXPECTED');

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
