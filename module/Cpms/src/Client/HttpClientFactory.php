<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Cpms\Client;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface as Logger;

class HttpClientFactory
{
    /**
     * @var ClientOptions
     */
    private $options;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(ClientOptions $options, Logger $logger)
    {
        $this->options = $options;
        $this->logger = $logger;
    }

    public function createHttpClient(): HttpClient
    {
        $clientConfig = [
            'timeout' => $this->options->getTimeout()
        ];

        $guzzleHttpClient = new Client($clientConfig);

        return new HttpClient($guzzleHttpClient, $this->options, $this->logger);
    }
}
