<?php

namespace Dvsa\Olcs\Api\Service\SecretsManager;

interface SecretsManagerInterface
{
    public function getSecrets(array $secretNames): array;

    public function getSecret(string $secretName): array;
}
