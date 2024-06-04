<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\SecretsManager;

class LocalSecretsManager extends AbstractSecretsManager implements SecretsManagerInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getSecret(string $secretName): array
    {
        if (array_key_exists($secretName, $this->config) && is_array($this->config[$secretName])) {
            return $this->config[$secretName];
        }
        throw new \InvalidArgumentException('Secret not found');
    }
}
