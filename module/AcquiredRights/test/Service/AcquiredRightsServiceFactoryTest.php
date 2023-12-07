<?php

namespace Dvsa\Olcs\AcquiredRights\Client;

use Dvsa\Olcs\AcquiredRights\Service\AcquiredRightsService;
use Dvsa\Olcs\AcquiredRights\Service\AcquiredRightsServiceFactory;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\OlcsTest\MocksServicesTrait;
use Laminas\Log\LoggerInterface;

class AcquiredRightsServiceFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    protected AcquiredRightsServiceFactory $sut;

    /**
     * @test
     */
    public function __invoke_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfAcquiredRightsClient(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsService::class);

        // Assert
        $this->assertInstanceOf(AcquiredRightsService::class, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function config_missingExpiry_ThrowsInvalidArgumentException(): void
    {
        // Setup
        $this->setUpSut();

        // Expectations
        $config = $this->config();
        unset($config['acquired_rights']['expiry']);
        $this->config($config);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration is undefined or empty(): acquired_rights -> expiry');

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsService::class);

        // Assert
        $this->assertInstanceOf(AcquiredRightsService::class, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function config_expiryNotInstanceOfDateTimeImmutable_ThrowsInvalidArgumentException(): void
    {
        // Setup
        $this->setUpSut();

        // Expectations
        $config = $this->config();
        $config['acquired_rights']['expiry'] = '1 Jan 1990';
        $this->config($config);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be instance of \DateTimeImmutable: acquired_rights -> expiry');

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsService::class);

        // Assert
        $this->assertInstanceOf(AcquiredRightsService::class, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function config_missingCheckEnabled_ThrowsInvalidArgumentException(): void
    {
        // Setup
        $this->setUpSut();

        // Expectations
        $config = $this->config();
        unset($config['acquired_rights']['check_enabled']);
        $this->config($config);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration is undefined or null: acquired_rights -> check_enabled');

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsService::class);

        // Assert
        $this->assertInstanceOf(AcquiredRightsService::class, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function config_checkEnabledNotInstanceOfBool_ThrowsInvalidArgumentException(): void
    {
        // Setup
        $this->setUpSut();

        // Expectations
        $config = $this->config();
        $config['acquired_rights']['check_enabled'] = 'true';
        $this->config($config);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be instance of bool: acquired_rights -> check_enabled');

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsService::class);

        // Assert
        $this->assertInstanceOf(AcquiredRightsService::class, $result);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new AcquiredRightsServiceFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->config();
        $this->loggerInterface();
        $this->acquiredRightsClient();
    }

    /**
     * @return array
     */
    protected function config(array $config = []): array
    {
        if (! $this->serviceManager->has('Config') || !empty($config)) {
            if (empty($config)) {
                $config = [
                    'acquired_rights' => [
                        // enables the expiry check, lookup of reference number, status check and dob comparison
                        'check_enabled' => true,
                        // determines when a user is no longer able to use an acquired rights reference number
                        'expiry' => \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC7231, 'Tue, 20 May 2025 22:59:59 GMT'), // Tue, 20 May 2025 23:59:59 BST
                        // guzzle client options
                        'client' => [ // Client configuration passed to Guzzle client. base_url is required and must be set to API root.
                            'base_uri' => 'http://127.0.0.1:3000/',
                            'timeout' => 30,
                        ],
                    ],
                ];
            }

            $this->serviceManager->setService('Config', $config);
        }

        return $this->serviceManager->get('Config');
    }

    protected function loggerInterface(): LoggerInterface
    {
        if (! $this->serviceManager->has('Logger')) {
            $logger = m::mock(LoggerInterface::class)->shouldIgnoreMissing()->byDefault();
            $this->serviceManager->setService('Logger', $logger);
        }
        return $this->serviceManager->get('Logger');
    }

    protected function acquiredRightsClient(): AcquiredRightsClient
    {
        if (! $this->serviceManager->has(AcquiredRightsClient::class)) {
            $logger = m::mock(AcquiredRightsClient::class)->shouldIgnoreMissing()->byDefault();
            $this->serviceManager->setService(AcquiredRightsClient::class, $logger);
        }
        return $this->serviceManager->get(AcquiredRightsClient::class);
    }
}
