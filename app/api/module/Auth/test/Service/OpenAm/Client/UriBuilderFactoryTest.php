<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service\OpenAm\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Auth\Client\UriBuilder;
use Dvsa\Olcs\Auth\Client\UriBuilderFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class UriBuilderFactoryTest extends MockeryTestCase
{
    public function testCreateService(): void
    {
        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [
                        'url' => 'url',
                        'realm' => 'realm',
                    ],
                ],
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with('Config')->andReturn($config);

        $sut = new UriBuilderFactory();
        $service = $sut($mockSl, UriBuilder::class);

        self::assertInstanceOf(UriBuilder::class, $service);
    }

    public function testCreateServiceMissingConfig(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(UriBuilderFactory::MSG_MISSING_URL);

        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [
                        'realm' => 'realm',
                    ],
                ],
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with('Config')->andReturn($config);

        $sut = new UriBuilderFactory();
        $sut($mockSl, UriBuilder::class);
    }
}
