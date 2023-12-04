<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Cpms\Authenticate;

class CpmsIdentityProviderFactory
{
    private $clientId;
    private $clientSecret;
    private $userId;

    public function __construct(string $clientId, string $clientSecret, string $userId)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->userId = $userId;
    }

    public function createCpmsIdentityProvider() : CpmsIdentityProvider
    {
        $identityProvider = new CpmsIdentityProvider($this->userId, $this->clientId, $this->clientSecret);
        return $identityProvider;
    }
}
