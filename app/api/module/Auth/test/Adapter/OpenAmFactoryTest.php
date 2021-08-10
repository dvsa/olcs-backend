<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Dvsa\Olcs\Auth\Adapter\OpenAm as OpenAmAdapter;
use Dvsa\Olcs\Auth\Adapter\OpenAmFactory;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OpenAmFactoryTest extends MockeryTestCase
{
    public function testCreateService(): void
    {
        $mockClient = m::mock(OpenAmClient::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with(OpenAmClient::class)->andReturn($mockClient);

        $sut = new OpenAmFactory();
        $service = $sut($mockSl, OpenAmAdapter::class);

        self::assertInstanceOf(OpenAmAdapter::class, $service);
    }
}
