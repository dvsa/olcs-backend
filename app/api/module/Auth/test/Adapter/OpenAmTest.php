<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Auth\Adapter\OpenAm as OpenAmAdapter;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Laminas\Authentication\Result as AuthResult;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OpenAmTest extends MockeryTestCase
{
    /**
     * @dataProvider dpAuthenticate
     */
    public function testChangePassword(): void
    {
        $username = 'username';
        $oldPassword = 'old password';
        $newPassword = 'new password';
        $realm = 'realm';
        $token = 'token';

        $authResponse = ['response'];

        $client = m::mock(OpenAmClient::class);
        $client->expects('changePassword')
            ->with($username, $oldPassword, $newPassword, $realm, $token)
            ->andReturn($authResponse);

        $identityProvider = m::mock(PidIdentityProvider::class);
        $identityProvider->expects('getToken')->withNoArgs()->andReturn($token);

        $sut = new OpenAmAdapter($client, $identityProvider);
        $sut->setRealm($realm);

        $this->assertEquals($authResponse, $sut->changePassword($username, $oldPassword, $newPassword));
    }

    public function testChangePasswordException(): void
    {
        $message = 'expected message';
        $this->expectException(ChangePasswordException::class);
        $this->expectExceptionMessage($message);

        $username = 'username';
        $oldPassword = 'old password';
        $newPassword = 'new password';
        $realm = 'realm';
        $token = 'token';

        $client = m::mock(OpenAmClient::class);
        $client->expects('changePassword')
            ->with($username, $oldPassword, $newPassword, $realm, $token)
            ->andThrow(\Exception::class, $message);

        $identityProvider = m::mock(PidIdentityProvider::class);
        $identityProvider->expects('getToken')->withNoArgs()->andReturn($token);

        $sut = new OpenAmAdapter($client, $identityProvider);
        $sut->setRealm($realm);

        $sut->changePassword($username, $oldPassword, $newPassword);
    }

    public function testAuthenticateWithException(): void
    {
        $identity = 'identity';
        $password = 'password';
        $realm = 'selfserve';

        $client = m::mock(OpenAmClient::class);
        $client->expects('authenticate')->with($identity, $password, $realm)
            ->andThrow(\Exception::class, 'message');

        $identityProvider = m::mock(PidIdentityProvider::class);

        $sut = new OpenAmAdapter($client, $identityProvider);
        $sut->setIdentity($identity);
        $sut->setCredential($password);
        $sut->setRealm($realm);

        $authResult = $sut->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_UNCATEGORIZED, $authResult->getCode());
    }

    /**
     * @dataProvider dpAuthenticate
     */
    public function testAuthenticate($authResponse, $resultCode): void
    {
        $identity = 'identity';
        $password = 'password';
        $realm = 'realm';

        $client = m::mock(OpenAmClient::class);
        $client->expects('authenticate')->with($identity, $password, $realm)->andReturn($authResponse);

        $identityProvider = m::mock(PidIdentityProvider::class);

        $sut = new OpenAmAdapter($client, $identityProvider);
        $sut->setIdentity($identity);
        $sut->setCredential($password);
        $sut->setRealm($realm);

        $authResult = $sut->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals($resultCode, $authResult->getCode());
    }

    public function dpAuthenticate(): array
    {
        return [
            'success ' => [
                ['status' => 200, 'tokenId' => 'tokenId'],
                AuthResult::SUCCESS
            ],
            'success with challenge' => [
                ['status' => 200, 'authId' => 'authId'],
                OpenAmAdapter::SUCCESS_WITH_CHALLENGE
            ],
            'failure'=> [
                ['status' => 401, 'message' => 'Unauthorised'],
                AuthResult::FAILURE
            ],
            'uncategorized' => [
                ['status' => null],
                AuthResult::FAILURE_UNCATEGORIZED
            ],
        ];
    }
}
