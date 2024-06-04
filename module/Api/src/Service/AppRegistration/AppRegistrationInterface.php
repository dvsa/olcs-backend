<?php

namespace Dvsa\Olcs\Api\Service\AppRegistration;

use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

interface AppRegistrationInterface
{
    public function __construct(Client $client, array $config, AppRegistrationSecret $secret, LoggerInterface $logger);
    public function getToken(): string;

    public function getClientId(): string;

    public function getScope(): string;

    public function getProxy(): string;

    public function getClientSecret(): string;

    public function getTokenUrl(): string;
}
