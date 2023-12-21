<?php

namespace Dvsa\Olcs\AcquiredRights\Client;

use Laminas\ServiceManager\ServiceManager;
use Dvsa\OlcsTest\MocksServicesTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AcquiredRightsClientFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected AcquiredRightsClientFactory $sut;

    /**
     * @test
     */
    public function invokeIsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeReturnsAnInstanceOfAcquiredRightsClient(): void
    {
        // Setup
        $this->setUpSut();

        $this->config();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsClient::class);

        // Assert
        $this->assertInstanceOf(AcquiredRightsClient::class, $result);
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function configmissingBaseUriThrowsInvalidArgumentException(): void
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
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsClient::class);
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function configemptyBaseUriThrowsInvalidArgumentException(): void
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
        $result = $this->sut->__invoke($this->serviceManager, AcquiredRightsClient::class);
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
