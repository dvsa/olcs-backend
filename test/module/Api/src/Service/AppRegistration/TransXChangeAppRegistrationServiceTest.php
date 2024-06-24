<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\AppRegistration;

use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret;
use Dvsa\Olcs\Api\Service\AppRegistration\TransXChangeAppRegistrationService;
use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use Psr\Log\LoggerInterface;

/**
 * @property m\LegacyMockInterface|m\MockInterface|\Psr\Log\LoggerInterface|(\Psr\Log\LoggerInterface&m\LegacyMockInterface)|(\Psr\Log\LoggerInterface&m\MockInterface)|Psr\Log\LoggerInterface|(Psr\Log\LoggerInterface&\Mockery\LegacyMockInterface)|(Psr\Log\LoggerInterface&\Mockery\MockInterface) $mockLogger
 */
class TransXChangeAppRegistrationServiceTest extends m\Adapter\Phpunit\MockeryTestCase
{
    protected TransXChangeAppRegistrationService $sut;
    protected Client $client;
    private $mockLogger;

    public function setUp(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"access_token": "test"}'),
            new RequestException('Error Communicating with Server', new Request('POST', '/token'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handlerStack]);
        $this->mockLogger = m::mock(LoggerInterface::class);

        parent::setUp();
    }

    /**
     * @dataProvider getConfig()
     */
    public function testGetToken(array $config): void
    {
        $mockLogger = m::mock(LoggerInterface::class);
        $mockLogger->shouldReceive('info')->once()->with('Access Token requested from TransXChange App Registration');
        $mockLogger->shouldReceive('debug')->once()->with('Access Token received from TransXChange App Registration test');
        $mockedSecret = m::mock(AppRegistrationSecret::class);
        $mockedSecret->shouldReceive('getClientSecret')->once()->andReturn('test');
        $this->sut = new TransXChangeAppRegistrationService(
            $this->client,
            $config,
            $mockedSecret,
            $mockLogger
        );

        $this->assertEquals('test', $this->sut->getToken());
    }

    /**
     * @dataProvider getConfig()
     *
     */
    public function testTokenThrowsException(array $config): void
    {
        $this->expectException(RequestException::class);
        // set up mock handler to throw exception
        $mock = new MockHandler([
              new RequestException('Error Communicating with Server', new Request('POST', '/token'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handlerStack]);
        $this->mockLogger->shouldReceive('info')->once()->with('Access Token requested from TransXChange App Registration');
        $this->mockLogger->shouldReceive('info')->once()->with('Access Token request failed from TransXChange App Registration');
        $mockedSecret = m::mock(AppRegistrationSecret::class);
        $mockedSecret->shouldReceive('getClientSecret')->once()->andReturn('test');
        $this->sut = new TransXChangeAppRegistrationService(
            $this->client,
            $config,
            $mockedSecret,
            $this->mockLogger
        );

        $this->sut->getToken();
    }

    public function getConfig(): array
    {
        return [
            [
                [
                    'secrets' => ['provider' => LocalSecretsManager::class],
                    'transxchange' => [
                        'token_url' => 'http://localhost',
                        'client_id' => 'abc123',
                        'scope' => 'abc123',
                        'secret_name' => 'client_secret'
                    ],
                    'proxy' => 'http://localhost',
                    'max_retry_attempts' => 3,
                ],
            ],
        ];
    }
}
