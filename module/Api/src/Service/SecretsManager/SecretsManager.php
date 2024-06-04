<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\SecretsManager;

use Aws\SecretsManager\SecretsManagerClient;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\Http\Client\Adapter\AdapterInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class SecretsManager extends AbstractSecretsManager implements SecretsManagerInterface
{
    private SecretsManagerClient $client;
    private CacheEncryption $cache;

    public function __construct(SecretsManagerClient $client, CacheEncryption $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
    }



    /**
     * @throws InvalidArgumentException
     * @throws \JsonException
     * @throws \Exception
     */
    public function getSecret(string $secretName): array
    {
        $secret = $this->cache->getCustomItem(CacheEncryption::SECRETS_MANAGER_IDENTIFIER, $secretName);
        if ($secret === null) {
            $secret = json_encode($this->loadSecret($secretName));
            $this->cache->setCustomItem(CacheEncryption::SECRETS_MANAGER_IDENTIFIER, $secret, $secretName);
        }
        return json_decode($secret, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    private function loadSecret(string $secretName): array
    {
        $secret = $this->client->getSecretValue([
            'SecretId' => $secretName,
        ]);

        return json_decode($secret['SecretString'], true, 512, JSON_THROW_ON_ERROR);
    }
}
