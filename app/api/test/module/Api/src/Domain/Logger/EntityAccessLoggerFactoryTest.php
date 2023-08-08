<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Logger;

use Olcs\TestHelpers\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Logger\EntityAccessLoggerFactory;
use Dvsa\Olcs\Api\Domain\Logger\EntityAccessLogger;
use LmcRbacMvc\Service\AuthorizationService;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Mockery\MockInterface;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Laminas\ServiceManager\ServiceManager;

/**
 * @see EntityAccessLoggerFactory
 */
class EntityAccessLoggerFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var EntityAccessLoggerFactory
     */
    protected $sut;

    /**
     * @test
     */
    public function invoke_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     */
    public function invoke_ReturnsInstanceOfFactoryProduct()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $product = $this->sut->__invoke($this->serviceManager, EntityAccessLogger::class);

        // Assert
        $this->assertInstanceOf(EntityAccessLogger::class, $product);
    }

    /**
     * @test
     * @deprecated
     */
    public function createService_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'createService']);
    }

    /**
     * @test
     * @deprecated
     */
    public function createService_ReturnsInstanceOfFactoryProduct()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $product = $this->sut->createService($this->serviceManager);

        // Assert
        $this->assertInstanceOf(EntityAccessLogger::class, $product);
    }

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    public function setUpSut(): void
    {
        $this->sut = new EntityAccessLoggerFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    public function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->authorizationService();
        $this->commandHandler();
    }

    /**
     * @return MockInterface|AuthorizationService
     */
    protected function authorizationService(): MockInterface
    {
        if (! $this->serviceManager->has(AuthorizationService::class)) {
            $instance = $this->setUpMockService(AuthorizationService::class);
            $this->serviceManager->setService(AuthorizationService::class, $instance);
        }
        return $this->serviceManager->get(AuthorizationService::class);
    }

    /**
     * @return MockInterface|CommandHandlerManager
     */
    protected function commandHandler(): MockInterface
    {
        if (! $this->serviceManager->has('CommandHandlerManager')) {
            $instance = $this->setUpMockService(CommandHandlerManager::class);
            $this->serviceManager->setService('CommandHandlerManager', $instance);
        }
        return $this->serviceManager->get('CommandHandlerManager');
    }
}
