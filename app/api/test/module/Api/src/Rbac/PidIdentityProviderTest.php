<?php

namespace Dvsa\OlcsTest\Api\Rbac;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Rbac\Identity;
use Dvsa\Olcs\Api\Rbac\IdentityProviderInterface;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Cli\Request\CliRequest;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Headers as HttpHeaders;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Http\PhpEnvironment\Request;

/**
 * Pid Identity Provider Test
 */
class PidIdentityProviderTest extends MockeryTestCase
{
    public function testGetIdentity()
    {
        $user = User::anon();
        $mockRepo = m::mock(RepositoryInterface::class);
        $mockRepo->shouldReceive('fetchByPid')->with('pid')->andReturn($user);

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getHeader')->with('X-Pid', m::any())->andReturnSelf();
        $mockRequest->shouldReceive('getFieldValue')->andReturn('pid');

        $sut = new PidIdentityProvider($mockRepo, $mockRequest, 'X-Pid', 'cookie name');

        $identity = $sut->getIdentity();

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertSame($user, $identity->getUser());
    }

    public function testGetIdentityAnon()
    {
        $mockRepo = m::mock(RepositoryInterface::class);

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getHeader')->with('X-Pid', m::any())->andReturnSelf();
        $mockRequest->shouldReceive('getFieldValue')->andReturn('');

        $sut = new PidIdentityProvider($mockRepo, $mockRequest, 'X-Pid', 'cookie name');

        $identity = $sut->getIdentity();

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertInstanceOf(User::class, $identity->getUser());
        $this->assertEquals(User::USER_TYPE_ANON, $identity->getUser()->getUserType());
    }

    public function testGetIdentitySystem()
    {
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)
            ->shouldReceive('getId')
            ->andReturn(IdentityProviderInterface::SYSTEM_USER)
            ->getMock();

        $mockRepo = m::mock(RepositoryInterface::class);
        $mockRepo->shouldReceive('fetchById')
            ->with(IdentityProviderInterface::SYSTEM_USER)
            ->andReturn($mockUser)
            ->once()
            ->getMock();

        $mockRequest = m::mock(CliRequest::class);

        $sut = new PidIdentityProvider($mockRepo, $mockRequest, 'X-Pid', 'cookie name');

        $identity = $sut->getIdentity();

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertInstanceOf(User::class, $identity->getUser());
        $this->assertEquals(IdentityProviderInterface::SYSTEM_USER, $identity->getUser()->getId());
    }

    public function testGetMasqueradedAsSystemUser()
    {
        $mockRepo = m::mock(RepositoryInterface::class);
        $mockRequest = m::mock(Request::class);
        $sut = new PidIdentityProvider($mockRepo, $mockRequest, 'X-Pid', 'cookie name');

        $sut->setMasqueradedAsSystemUser(true);
        $this->assertTrue($sut->getMasqueradedAsSystemUser());
    }

    public function testGetToken(): void
    {
        $mockRepo = m::mock(RepositoryInterface::class);

        $cookieName = 'cookie name';
        $token = 'test';
        $cookie = new Cookie();
        $cookie->{$cookieName} = $token;

        $httpHeaders = m::mock(HttpHeaders::class);
        $httpHeaders->expects('get')->with('Cookie')->andReturn($cookie);

        $mockRequest = m::mock(Request::class);
        $mockRequest->expects('getHeaders')->withNoArgs()->andReturn($httpHeaders);

        $sut = new PidIdentityProvider($mockRepo, $mockRequest, 'X-Pid', $cookieName);
        $this->assertEquals($token, $sut->getToken());
    }

    public function testGetTokenEmptyCookie(): void
    {
        $mockRepo = m::mock(RepositoryInterface::class);

        $httpHeaders = m::mock(HttpHeaders::class);
        $httpHeaders->expects('get')->with('Cookie')->andReturn(new Cookie());

        $mockRequest = m::mock(Request::class);
        $mockRequest->expects('getHeaders')->withNoArgs()->andReturn($httpHeaders);

        $sut = new PidIdentityProvider($mockRepo, $mockRequest, 'X-Pid', 'cookie name');
        $this->assertNull($sut->getToken());
    }
}
