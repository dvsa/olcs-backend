<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Auth\Client\OpenAm;
use Dvsa\Olcs\Auth\Client\OpenAmFactory;
use Dvsa\Olcs\Auth\Client\UriBuilder;
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
        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [
                        'client' => [
                            'options' => ['options'],
                        ],
                        'cookie' => [
                            'name' => 'cookie name',
                        ],
                    ],
                ],
            ],
        ];

        $mockUriBuilder = m::mock(UriBuilder::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with('Config')->andReturn($config);
        $mockSl->expects('get')->with(UriBuilder::class)->andReturn($mockUriBuilder);

        $sut = new OpenAmFactory();
        $service = $sut($mockSl, OpenAm::class);

        self::assertInstanceOf(OpenAm::class, $service);
    }

    public function testCreateServiceMissingOptionConfig(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(OpenAmFactory::MSG_MISSING_OPTIONS);

        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [
                        'client' => [],
                    ],
                ],
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with('Config')->andReturn($config);

        $sut = new OpenAmFactory();
        $sut($mockSl, OpenAm::class);
    }

    public function testCreateServiceMissingCookieConfig(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(OpenAmFactory::MSG_MISSING_COOKIE_NAME);

        $config = [
            'auth' => [
                'adapters' => [
                    'openam' => [
                        'client' => [
                            'options' => ['options'],
                        ],
                        'cookie' => [],
                    ],
                ],
            ],
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('get')->with('Config')->andReturn($config);

        $sut = new OpenAmFactory();
        $sut($mockSl, OpenAm::class);
    }
}
