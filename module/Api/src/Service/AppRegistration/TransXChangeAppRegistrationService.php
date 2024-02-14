<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\AppRegistration;

use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class TransXChangeAppRegistrationService implements AppRegistrationInterface
{
    use GetAccessTokenTrait;

    protected Client $client;
    protected LoggerInterface $logger;

    private const APP_REGISTRATION_NAME = 'transxchange';
    private AppRegistrationSecret $secret;
    private array $config;

    public function __construct(Client $client, array $config, AppRegistrationSecret $secret, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->secret = $secret;
        $this->client = $client;
        $this->logger = $logger;
    }

    public function getClientId(): string
    {
        return $this->config[self::APP_REGISTRATION_NAME]['client_id'];
    }

    public function getScope(): string
    {
        return $this->config[self::APP_REGISTRATION_NAME]['scope'];
    }

    public function getProxy(): string
    {
        return $this->config['proxy'];
    }

    public function getClientSecret(): string
    {
        return $this->secret->getClientSecret($this->config[self::APP_REGISTRATION_NAME]['secret_name']);
    }

    public function getTokenUrl(): string
    {
        return $this->config[self::APP_REGISTRATION_NAME]['token_url'];
    }

    /**
     * @throws GuzzleException
     */
    public function getToken(): string
    {
        $this->logger->info('Access Token requested from TransXChange App Registration');
        try {
             $accessToken = $this->getAccessToken($this->client);
        } catch (GuzzleException $e) {
            $this->logger->info('Access Token request failed from TransXChange App Registration');
            throw $e;
        }
        $this->logger->debug('Access Token received from TransXChange App Registration ' . $accessToken);
        return $accessToken;
    }
}
