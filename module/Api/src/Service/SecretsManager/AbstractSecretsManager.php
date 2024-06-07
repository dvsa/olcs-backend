<?php

namespace Dvsa\Olcs\Api\Service\SecretsManager;

use Psr\SimpleCache\InvalidArgumentException;

abstract class AbstractSecretsManager implements SecretsManagerInterface
{
    public function getSecrets(array $secretNames): array
    {
        $secrets = [];
        foreach ($secretNames as $secretName) {
            $secrets[$secretName] = $this->getSecret($secretName);
        }
        return $secrets;
    }
}
