<?php

namespace Dvsa\OlcsTest\Api\Rbac;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Api\Rbac\PidIdentityProviderFactory;
use Interop\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Http\PhpEnvironment\Request;

class PidIdentityProviderFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockRequest = m::mock(Request::class);
        $mockUserRepo = m::mock(RepositoryInterface::class);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('RepositoryServiceManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $mockSl->shouldReceive('get')->with('User')->andReturn($mockUserRepo);
        $mockSl->shouldReceive('get')->with('Config')->andReturn(
            [
                'openam' => [
                    'pid_header' => 'X-Pid',
                ],
                'auth' => [
                    'adapters' => [
                        'openam' => [
                            'cookie' => [
                                'name' => 'cookie name',
                            ],
                        ]
                    ]
                ]
            ]
        );

        $sut = new PidIdentityProviderFactory();

        $service = $sut->__invoke($mockSl, PidIdentityProvider::class);

        $this->assertInstanceOf(PidIdentityProvider::class, $service);
    }
}
