<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Auth\Client\UriBuilder;
use Dvsa\Olcs\Auth\Client\UriBuilderFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see UriBuilderFactory
 */
class UriBuilderFactoryTest extends MockeryTestCase
{
    public function testCreateService(): void
    {
        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [
                        'urls' => [
                            'internal' => 'internal',
                            'selfserve' => 'selfserve',
                        ]
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

    public function testCreateServiceMissingInternalUrlConfig(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(UriBuilderFactory::MSG_MISSING_INTERNAL_URL);

        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [],
                ],
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with('Config')->andReturn($config);

        $sut = new UriBuilderFactory();
        $sut($mockSl, UriBuilder::class);
    }

    public function testCreateServiceMissingSelfserviceUrlConfig(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(UriBuilderFactory::MSG_MISSING_SELFSERVE_URL);

        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [
                        'urls' => [
                            'internal' => 'internal url'
                        ]
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
