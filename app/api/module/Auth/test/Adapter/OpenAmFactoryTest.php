<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Auth\Adapter\OpenAm as OpenAmAdapter;
use Dvsa\Olcs\Auth\Adapter\OpenAmFactory;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see OpenAmFactory
 */
class OpenAmFactoryTest extends MockeryTestCase
{
    public function testCreateService(): void
    {
        $mockClient = m::mock(OpenAmClient::class);
        $identityProvider = m::mock(PidIdentityProvider::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with(OpenAmClient::class)->andReturn($mockClient);
        $mockSl->expects('get')->with(PidIdentityProvider::class)->andReturn($identityProvider);

        $sut = new OpenAmFactory();
        $service = $sut($mockSl, OpenAmAdapter::class);

        self::assertInstanceOf(OpenAmAdapter::class, $service);
    }
}
