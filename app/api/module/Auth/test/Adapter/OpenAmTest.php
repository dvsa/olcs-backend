<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Dvsa\Olcs\Auth\Adapter\OpenAm as OpenAmAdapter;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Laminas\Authentication\Result as AuthResult;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OpenAmTest extends MockeryTestCase
{
    public function testAuthenticateWithException(): void
    {
        $identity = 'identity';
        $password = 'password';

        $client = m::mock(OpenAmClient::class);
        $client->expects('authenticate')->with($identity, $password)
            ->andThrow(\Exception::class);

        $sut = new OpenAmAdapter($client);
        $sut->setIdentity($identity);
        $sut->setCredential($password);

        $authResult = $sut->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_UNCATEGORIZED, $authResult->getCode());
    }


    /**
     * @dataProvider dpAuthenticate
     */
    public function testAuthenticate($authStatus, $resultCode): void
    {
        $identity = 'identity';
        $password = 'password';

        $authResponse = [
            'status' => $authStatus
        ];

        $client = m::mock(OpenAmClient::class);
        $client->expects('authenticate')->with($identity, $password)->andReturn($authResponse);

        $sut = new OpenAmAdapter($client);
        $sut->setIdentity($identity);
        $sut->setCredential($password);

        $authResult = $sut->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals($resultCode, $authResult->getCode());
    }

    public function dpAuthenticate(): array
    {
        return [
            [200, AuthResult::SUCCESS],
            [401, AuthResult::FAILURE],
            [null, AuthResult::FAILURE_UNCATEGORIZED],
        ];
    }
}
