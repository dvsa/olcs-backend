<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\AppRegistration;

use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret;
use Dvsa\Olcs\Api\Service\AppRegistration\TransXChangeAppRegistrationService;
use Dvsa\Olcs\Api\Service\AppRegistration\AppRegistrationServiceFactory;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\Log\Logger as Logger;
use Mockery as m;
use Psr\Container\ContainerInterface;

class AppRegistrationServiceFactoryTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testInvoke(): void
    {
        $config = [
            'cache-encryption' => ['secrets' => ['shared' => 'test']],
            'app-registrations' => [
                'transxchange' => [
                    'token_url' => 'http://localhost',
                    'clientId' => 'abc123',
                    'scope' => 'abc123',
                    'secret_name' => 'client_secret'
                ],
                'proxy' => 'http://localhost',
                'max_retry_attempts' => 3,
            ]
        ];

        $logger = new Logger();

        $mockSl = m::mock(ContainerInterface::class);
        $mockAppRegistrationSecret = m::mock(AppRegistrationSecret::class);
        $mockCache = m::mock(CacheEncryption::class);
        $mockCache->shouldReceive('setEncryptionKey')->with('test');
        $mockCache->shouldReceive('getCustomItem')->with(CacheEncryption::SECRETS_MANAGER_IDENTIFIER, 'client_secret')
            ->andReturn(json_encode(['client_secret' => 'test']));

        $mockSl->shouldReceive('get')->with(CacheEncryption::class)->andReturn($mockCache);
        $mockSl->shouldReceive('get')->with('config')->andReturn($config);
        $mockSl->shouldReceive('get')->with('Logger')->andReturn($logger);
        $mockSl->shouldReceive('get')->with(AppRegistrationSecret::class)->andReturn($mockAppRegistrationSecret);
        $sut = new AppRegistrationServiceFactory();
        $service = $sut->__invoke($mockSl, TransXChangeAppRegistrationService::class, []);

        $this->assertInstanceOf(TransXChangeAppRegistrationService::class, $service);
    }
}
