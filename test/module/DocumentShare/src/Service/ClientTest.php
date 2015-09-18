<?php

namespace Dvsa\OlcsTest\DocumentShare\Service;

use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Dvsa\Olcs\DocumentShare\Service\Client;

/**
 * Client Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBaseUri()
    {
        $sut = new Client();
        $this->assertEmpty($sut->getBaseUri());
    }

    public function testSetBaseUri()
    {
        $sut = new Client();
        $baseUri = 'http://testing';
        $sut->setBaseUri($baseUri);
        $this->assertEquals($baseUri, $sut->getBaseUri());
    }

    public function testGetHttpClient()
    {
        $sut = new Client();
        $this->assertNull($sut->getHttpClient());
    }

    public function testSetHttpClient()
    {
        $sut = new Client();
        $mockClient = $this->getMock('Zend\Http\Client');
        $sut->setHttpClient($mockClient);

        $this->assertSame($mockClient, $sut->getHttpClient());
    }

    public function testGetRequestTemplate()
    {
        $sut = new Client();
        $this->assertNull($sut->getRequestTemplate());
    }

    public function testSetRequestTemplate()
    {
        $sut = new Client();
        $mockRequest = $this->getMock('Zend\Http\Request');
        $sut->setRequestTemplate($mockRequest);

        $this->assertSame($mockRequest, $sut->getRequestTemplate());
    }

    public function testGetWorkspace()
    {
        $sut = new Client();
        $this->assertEmpty($sut->getWorkspace());
    }

    public function testSetWorkspace()
    {
        $sut = new Client();
        $workspace = 'test';
        $sut->setWorkspace($workspace);

        $this->assertEquals($workspace, $sut->getWorkspace());
    }

    /**
     * @dataProvider provideRead
     * @param $code
     * @param $content
     * @param $expected
     */
    public function testRead($code, $content, $expected)
    {
        $mockRequest = $this->getMock('Zend\Http\Request');
        $mockRequest->expects($this->once())
            ->method('setUri')
            ->with('http://testing/content/testing/test')
            ->willReturnSelf();

        $mockRequest->expects($this->once())
            ->method('setMethod')
            ->with('GET')
            ->willReturnSelf();

        $mockResponse = $this->getMock('Zend\Http\Response');
        $mockResponse->expects($this->once())->method('getStatusCode')->will($this->returnValue($code));
        $mockResponse->expects($this->any())->method('getBody')->willReturn($content);

        $mockClient = $this->getMock('Zend\Http\Client');
        $mockClient
            ->expects($this->once())
            ->method('setRequest')
            ->with($mockRequest)
            ->willReturnSelf();

        $mockClient->expects($this->once())->method('send')->willReturn($mockResponse);

        $sut = new Client();
        $sut->setRequestTemplate($mockRequest);
        $sut->setWorkspace('testing');
        $sut->setBaseUri('http://testing/');
        $sut->setHttpClient($mockClient);
        $data = $sut->read('test');

        $this->assertEquals($expected, $data);

    }

    public function provideRead()
    {
        $f = new File();
        $f->setContent(base64_encode('testing'));

        $expected = new File();
        $expected->setContent('testing');

        return array(
            array(404, '', null),
            array(200, json_encode($f->getArrayCopy()), $expected)
        );
    }

    public function testWrite()
    {
        $mockRequest = $this->getMock('Zend\Http\Request');
        $mockRequest->expects($this->once())
            ->method('setUri')
            ->with('http://testing/content/testing')
            ->willReturnSelf();

        $mockRequest->expects($this->once())
            ->method('setMethod')
            ->with('POST')
            ->willReturnSelf();

        $mockRequest->expects($this->once())
            ->method('setContent')
            ->with('{"content":"dGVzdA==","hubPath":"test","mime":"foo"}')
            ->willReturnSelf();

        $mockHeaders = $this->getMock('\stdClass', ['addHeaderLine']);

        $mockHeaders->expects($this->once())
            ->method('addHeaderLine')
            ->with('Content-Type', 'application/json');

        $mockRequest->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($mockHeaders));

        $mockFile = $this->getMock('Dvsa\Olcs\DocumentShare\Data\Object\File');
        $mockFile->expects($this->once())->method('getArrayCopy')->willReturn(array('content'=>'test'));
        $mockFile->expects($this->once())->method('getRealType')->willReturn('foo');

        $mockHttpClient = $this->getMock('Zend\Http\Client');
        $mockHttpClient
            ->expects($this->once())
            ->method('setRequest')
            ->with($mockRequest)
            ->willReturnSelf();

        $mockHttpClient->expects($this->once())->method('send')->willReturn('response');

        $sut = new Client();
        $sut->setRequestTemplate($mockRequest);
        $sut->setHttpClient($mockHttpClient);
        $sut->setWorkspace('testing');
        $sut->setBaseUri('http://testing/');
        $response = $sut->write('test', $mockFile);

        $this->assertEquals('response', $response);
    }

    /**
     * @dataProvider provideRemove
     */
    public function testRemove($uri, $hard)
    {
        $mockRequest = $this->getMock('Zend\Http\Request');
        $mockRequest->expects($this->once())
            ->method('setUri')
            ->with($uri)
            ->willReturnSelf();

        $mockRequest->expects($this->once())
            ->method('setMethod')
            ->with('DELETE');

        $mockClient = $this->getMock('Zend\Http\Client');
        $mockClient
            ->expects($this->once())
            ->method('setRequest')
            ->with($mockRequest)
            ->willReturnSelf();

        $mockClient->expects($this->once())->method('send')->willReturn('fake response');

        $sut = new Client();
        $sut->setRequestTemplate($mockRequest);
        $sut->setWorkspace('testing');
        $sut->setBaseUri('http://testing/');
        $sut->setHttpClient($mockClient);
        $result = $sut->remove('test', $hard);

        $this->assertEquals('fake response', $result);
    }

    public function provideRemove()
    {
        return array(
            array('http://testing/content/testing/test', false),
            array('http://testing/version/content/testing/test', true)
        );
    }
}
