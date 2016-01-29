<?php

/**
 * Client Test
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\Client;
use Zend\Http\Client as HttpClient;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * Client Test
 */
class ClientTest extends MockeryTestCase
{
    public function testRegisterUser()
    {
        $sentRequest = null;

        $return = function ($request) use (&$sentRequest) {
            $sentRequest = $request;
            return new Response();
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->registerUser('username', 'pid', 'email', 'surname', 'commonname', 'internal', 'password');

        $this->assertInstanceOf(Request::class, $sentRequest);
        $this->assertNotSame($request, $sentRequest);
        $this->assertStringStartsWith(
            'POST http://testing.com:80/users?_action=create',
            $sentRequest->renderRequestLine()
        );
        $expected = [
            '_id' => 'pid',
            'pid' => 'pid',
            'emailAddress' => 'email',
            'surName' => 'surname',
            'commonName' => 'commonname',
            'realm' => 'internal',
            'password' => 'password',
            'userName' => 'username'
        ];
        $this->assertEquals($expected, json_decode($sentRequest->getContent(), JSON_OBJECT_AS_ARRAY));
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException
     */
    public function testRegisterUserError()
    {
        $return = function ($request) {
            $resp = new Response();
            $resp->setStatusCode(500);
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->registerUser('username', 'pid', 'email', 'surname', 'commonname', 'internal', 'password');
    }

    public function testUpdateUser()
    {
        $sentRequest = null;

        $return = function ($request) use (&$sentRequest) {
            $sentRequest = $request;
            return new Response();
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->updateUser('pid', [['operation' => 'replace', 'field' => 'emailAddress', 'value' => 'email2']]);

        $this->assertInstanceOf(Request::class, $sentRequest);
        $this->assertNotSame($request, $sentRequest);
        $this->assertStringStartsWith(
            'PATCH http://testing.com:80/users/pid',
            $sentRequest->renderRequestLine()
        );
        $expected = [['operation' => 'replace', 'field' => 'emailAddress', 'value' => 'email2']];
        $this->assertEquals($expected, json_decode($sentRequest->getContent(), JSON_OBJECT_AS_ARRAY));
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException
     */
    public function testUpdateUserError()
    {
        $return = function ($request) {
            $resp = new Response();
            $resp->setStatusCode(500);
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->updateUser('pid', [['operation' => 'replace', 'field' => 'emailAddress', 'value' => 'email2']]);
    }
}
