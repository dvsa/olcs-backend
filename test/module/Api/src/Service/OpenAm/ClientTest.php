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

    public function testFetchUser()
    {
        $expected = ['pid' => 'some-pid'];

        $sentRequest = null;

        $return = function ($request) use (&$sentRequest, $expected) {
            $sentRequest = $request;

            $resp = new Response();
            $resp->setContent(json_encode($expected));
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $userData = $sut->fetchUser('some-pid');

        $this->assertInstanceOf(Request::class, $sentRequest);
        $this->assertNotSame($request, $sentRequest);
        $this->assertStringStartsWith(
            'GET http://testing.com:80/users/some-pid',
            $sentRequest->renderRequestLine()
        );
        $this->assertEquals($expected, $userData);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException
     */
    public function testFetchUserError()
    {
        $return = function () {
            $resp = new Response();
            $resp->setStatusCode(500);
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->fetchUser('some-pid');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFetchUserJsonError()
    {
        $return = function () {
            $resp = new Response();
            $resp->setContent('invalid json');
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->fetchUser('some-pid');
    }

    public function testFetchUsers()
    {
        $expectedResult = [
            [
                'pid' => 'some-pid-1'
            ],
            [
                'pid' => 'some-pid-2'
            ]
        ];

        $httpResponseContent = [
            'result' => $expectedResult
        ];

        $sentRequest = null;

        $return = function ($request) use (&$sentRequest, $httpResponseContent) {
            $sentRequest = $request;

            $resp = new Response();
            $resp->setContent(json_encode($httpResponseContent));
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $userData = $sut->fetchUsers(['some-pid-1', 'some-pid-2']);

        $this->assertInstanceOf(Request::class, $sentRequest);
        $this->assertNotSame($request, $sentRequest);
        $queryString = urlencode('pid eq "some-pid-1" or pid eq "some-pid-2"');
        $this->assertStringStartsWith(
            'GET http://testing.com:80/users?_queryFilter='.$queryString,
            $sentRequest->renderRequestLine()
        );
        $this->assertEquals($expectedResult, $userData);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException
     */
    public function testFetchUsersError()
    {
        $return = function () {
            $resp = new Response();
            $resp->setStatusCode(500);
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->fetchUsers(['some-pid']);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFetchUsersJsonError()
    {
        $return = function () {
            $resp = new Response();
            $resp->setContent('invalid json');
            return $resp;
        };

        $mockClient = m::mock(HttpClient::class);
        $mockClient->shouldReceive('send')->with(m::type(Request::class))->andReturnUsing($return);

        $request = new Request();
        $request->setUri('http://testing.com');

        $sut = new Client($mockClient, $request);

        $sut->fetchUsers(['some-pid']);
    }
}
