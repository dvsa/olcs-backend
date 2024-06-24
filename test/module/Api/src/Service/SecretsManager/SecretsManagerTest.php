<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\SecretsManager;

use Aws\SecretsManager\SecretsManagerClient;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManager;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @property SecretsManager $sut
 */
class SecretsManagerTest extends m\Adapter\Phpunit\MockeryTestCase
{
    private SecretsManager $sut;
    private $mockClient;
    private $mockCache;
    public function setUp(): void
    {
        $this->mockClient = m::mock(SecretsManagerClient::class);
        $this->mockCache = m::mock(CacheEncryption::class);
        $this->sut = new SecretsManager(
            $this->mockClient,
            $this->mockCache
        );
        parent::setUp();
    }

    /**
     * @throws InvalidArgumentException
     * @throws \JsonException
     */
    public function testGetSecretsCallsCache(): void
    {
        $secretName = 'testSecret';
        $secretValue = 'testSecretValue';
        $this->mockCache->shouldReceive('getCustomItem')->with(CacheEncryption::SECRETS_MANAGER_IDENTIFIER, $secretName)
            ->andReturn(json_encode(['client_secret' => $secretValue]));
        self::assertEquals($secretValue, $this->sut->getSecret($secretName)['client_secret']);
    }

    /**
     * @throws InvalidArgumentException
     * @throws \JsonException
     */
    public function testCacheMiss(): void
    {
        $secretName = 'testSecret';
        $secretValue = 'testSecretValue';
        $this->mockCache->shouldReceive('getCustomItem')->with(CacheEncryption::SECRETS_MANAGER_IDENTIFIER, $secretName)
            ->andReturn(null);
        $this->mockCache->shouldReceive('setCustomItem')->with(CacheEncryption::SECRETS_MANAGER_IDENTIFIER, '{"testSecret":{"client_secret":"testSecretValue"}}', $secretName);
        $this->mockClient->shouldReceive('getSecretValue')->with(['SecretId' => $secretName,])->andReturn(['SecretString' => json_encode([ $secretName => ["client_secret" => $secretValue]])]);
        self::assertEquals([$secretName => ['client_secret' => $secretValue]], $this->sut->getSecret($secretName));
    }
}
