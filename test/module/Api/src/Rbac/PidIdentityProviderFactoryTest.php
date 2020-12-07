<?php

/**
 * Pid Identity Provider Factory Test
 */
namespace Dvsa\OlcsTest\Api\Rbac;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Api\Rbac\PidIdentityProviderFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Pid Identity Provider Factory Test
 */
class PidIdentityProviderFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockRequest = m::mock(Request::class);
        $mockUserRepo = m::mock(RepositoryInterface::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('RepositoryServiceManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $mockSl->shouldReceive('get')->with('User')->andReturn($mockUserRepo);
        $mockSl->shouldReceive('get')->with('Config')->andReturn(['openam' => ['pid_header' => 'X-Pid']]);

        $sut = new PidIdentityProviderFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(PidIdentityProvider::class, $service);
    }
}
