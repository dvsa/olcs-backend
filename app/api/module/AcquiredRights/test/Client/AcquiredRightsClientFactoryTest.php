<?php

namespace Dvsa\Olcs\AcquiredRights\Client;

use Laminas\ServiceManager\ServiceManager;
use Olcs\TestHelpers\MockeryTestCase;
use Mockery as m;
use Olcs\TestHelpers\Service\MocksServicesTrait;

class AcquiredRightsClientFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    protected AcquiredRightsClientFactory $sut;

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
     */
    public function createService_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'createService']);
    }

    /**
     * @test
     * @depends createService_IsCallable
     * @depends __invoke_IsCallable
     */
    public function createService_CallsInvoke(): void
    {
        // Setup
        $this->sut = m::mock(AcquiredRightsClientFactory::class)->makePartial();

        // Expectations
        $this->sut->expects('__invoke')->withArgs(function ($serviceManager, $requestedName) {
            $this->assertSame($this->serviceManager(), $serviceManager, 'Expected first argument to be the ServiceManager passed to createService');
            $this->assertSame(AcquiredRightsClient::class, $requestedName, 'Expected requestedName to be class reference');
            return true;
        });

        // Execute
        $this->sut->createService($this->serviceManager());
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
        $result = $this->sut->__invoke($this->pluginManager(), AcquiredRightsClient::class);

        // Assert
        $this->assertInstanceOf(AcquiredRightsClient::class, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function config_missingBaseUri_ThrowsInvalidArgumentException(): void
    {
        // Setup
        $this->setUpSut();

        // Expectations
        $config = $this->config();
        unset($config['acquired_rights']['client']['base_uri']);
        $this->config($config);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected configuration defined and not empty: acquired_rights -> client -> base_uri');

        // Execute
        $result = $this->sut->__invoke($this->pluginManager(), AcquiredRightsClient::class);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function config_emptyBaseUri_ThrowsInvalidArgumentException(): void
    {
        // Setup
        $this->setUpSut();

        // Expectations
        $config = $this->config();
        $config['acquired_rights']['client']['base_uri'] = '';
        $this->config($config);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected configuration defined and not empty: acquired_rights -> client -> base_uri');

        // Execute
        $result = $this->sut->__invoke($this->pluginManager(), AcquiredRightsClient::class);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new AcquiredRightsClientFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->config();
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
}
