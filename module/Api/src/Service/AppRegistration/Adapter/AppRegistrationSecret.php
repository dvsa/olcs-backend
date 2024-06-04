<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\AppRegistration\Adapter;

use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManagerInterface;

class AppRegistrationSecret
{
    private SecretsManagerInterface $secretsManager;



    public function __construct(SecretsManagerInterface $secretsManager)
    {
        $this->secretsManager = $secretsManager;
    }

    public function getClientSecret(string $secretName): string
    {
        return $this->secretsManager->getSecret($secretName)['client_secret'];
    }
}
