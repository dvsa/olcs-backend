<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ChangeExpiredPassword;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ChangeExpiredPasswordFactory;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Dvsa\OlcsTest\MocksRepositoriesTrait;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;

/**
 * @see ChangeExpiredPasswordFactory
 */
class ChangeExpiredPasswordFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksRepositoriesTrait;
    use MocksAbstractCommandHandlerServicesTrait;

    /**
     * @var ChangeExpiredPasswordFactory
     */
    protected $sut;

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
        $this->sut = m::mock(ChangeExpiredPasswordFactory::class)->makePartial();

        // Expectations
        $this->sut->expects('__invoke')->withArgs(function ($serviceManager, $requestedName) {
            $this->assertSame($this->serviceManager(), $serviceManager, 'Expected first argument to be the ServiceManager passed to createService');
            $this->assertSame(ChangeExpiredPassword::class, $requestedName, 'Expected requestedName to be ' . ChangeExpiredPassword::class);
            return true;
        });

        // Execute
        $this->sut->createService($this->serviceManager());
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfChangeExpiredPasswordCommandHandler(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->pluginManager(), null);

        // Assert
        $this->assertInstanceOf(ChangeExpiredPassword::class, $result);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new ChangeExpiredPasswordFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager): void
    {
        $this->adapter();
        $this->setUpAbstractCommandHandlerServices();
    }

    /**
     * @return ValidatableAdapterInterface|m\MockInterface
     */
    protected function adapter(): m\MockInterface
    {
        if (! $this->serviceManager->has(ValidatableAdapterInterface::class)) {
            $this->serviceManager->setService(
                ValidatableAdapterInterface::class,
                $this->setUpMockService(ValidatableAdapterInterface::class)
            );
        }

        return $this->serviceManager->get(ValidatableAdapterInterface::class);
    }
}
