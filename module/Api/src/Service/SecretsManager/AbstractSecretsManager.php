<?php

namespace Dvsa\Olcs\Api\Service\SecretsManager;

use Psr\SimpleCache\InvalidArgumentException;

abstract class AbstractSecretsManager
{
    /**
     * @throws InvalidArgumentException
     * @throws \JsonException
     */
    public function getSecrets(array $secretNames): array
    {
        $secrets = [];
        foreach ($secretNames as $secretName) {
            $secrets[$secretName] = $this->getSecret($secretName);
        }
        return $secrets;
    }
}
