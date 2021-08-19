<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Rbac;

use Dvsa\Olcs\Api\Rbac\IdentityProviderFactory;
use Dvsa\Olcs\Api\Rbac\IdentityProviderInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Options\ModuleOptions;

class IdentityProviderFactoryTest extends MockeryTestCase
{
    public function testInvoke()
    {
        $entityProviderClassName = 'IdentityProviderInterfaceClassName';
        $mockModuleOptions = m::mock(ModuleOptions::class);
        $mockModuleOptions->expects('getIdentityProvider')->andReturn($entityProviderClassName);
        $mockIdentityProvider = m::mock(IdentityProviderInterface::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with(ModuleOptions::class)->andReturn($mockModuleOptions);
        $mockSl->expects('get')->with($entityProviderClassName)->andReturn($mockIdentityProvider);

        $sut = new IdentityProviderFactory();
        $service = $sut($mockSl, IdentityProviderInterface::class);

        self::assertInstanceOf(IdentityProviderInterface::class, $service);
    }
}
