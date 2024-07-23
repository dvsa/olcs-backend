<?php

namespace Dvsa\Olcs\DvsaAddressService\Client\Test;

use Dvsa\Olcs\DvsaAddressService\Client\DvsaAddressServiceClientFactory;
use Dvsa\Olcs\DvsaAddressService\Client\DvsaAddressServiceClient;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class TestableDvsaAddressServiceClientFactory extends DvsaAddressServiceClientFactory {
    public bool $getAppRegistrationServiceTokenCalled = false;
    protected function getAppRegistrationServiceToken(array $config): string {
        $this->getAppRegistrationServiceTokenCalled = true;
        return 'mock_access_token';
    }
}

class DvsaAddressServiceClientFactoryTest extends TestCase
{
    private \PHPUnit\Framework\MockObject\MockObject|ContainerInterface $container;
    private TestableDvsaAddressServiceClientFactory $factory;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new TestableDvsaAddressServiceClientFactory();
    }

    public function testInvokeReturnsDvsaAddressServiceClientWhenAuthorizationHeaderOverridden()
    {
        $config = [
            'dvsa_address_service' => [
                'client' => [
                    'base_uri' => 'http://example.com',
                    'headers' => [
                        'Authorization' => 'Bearer someToken'
                    ]
                ],
                'oauth2' => [
                    'client_id' => 'testClientId',
                    'client_secret' => 'testClientSecret',
                    'token_url' => 'http://example.com/token',
                    'scope' => 'testScope'
                ]
            ]
        ];

        $this->container->method('get')
            ->willReturnMap([
                ['config', $config],
            ]);

        $result = $this->factory->__invoke($this->container, DvsaAddressServiceClient::class);

        $this->assertInstanceOf(DvsaAddressServiceClient::class, $result);
        $this->assertFalse($this->factory->getAppRegistrationServiceTokenCalled);
    }

    public function testInvokeHandlesMissingConfig()
    {
        $this->container->method('get')
            ->willReturn([]);

        $this->expectException(\RuntimeException::class);

        $this->factory->__invoke($this->container, DvsaAddressServiceClient::class);
    }

    public function testInvokeReturnsDvsaAddressServiceClientWhenAuthorizationHeaderIsNotOverriddenAndUsesGetAppRegistrationToken()
    {
        $config = [
            'dvsa_address_service' => [
                'client' => [
                    'base_uri' => 'http://example.com',
                    'headers' => [] // No Authorization header overriden
                ],
                'oauth2' => [
                    'client_id' => 'testClientId',
                    'client_secret' => 'testClientSecret',
                    'token_url' => 'http://example.com/token',
                    'scope' => 'testScope'
                ]
            ]
        ];

        $this->container->method('get')->willReturnMap([
            ['config', $config],
        ]);

        $result = $this->factory->__invoke($this->container, DvsaAddressServiceClient::class);

        $this->assertInstanceOf(DvsaAddressServiceClient::class, $result);
        $this->assertTrue($this->factory->getAppRegistrationServiceTokenCalled);
    }
}
