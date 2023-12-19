<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ForgotPassword;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ForgotPasswordFactory;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Dvsa\OlcsTest\MocksServicesTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ForgotPasswordFactory
 */
class ForgotPasswordFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;

    /**
     * @var ForgotPasswordFactory
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
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfForgotPasswordCommandHandler()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, ForgotPassword::class);

        // Assert
        $this->assertInstanceOf(ForgotPassword::class, $result);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new ForgotPasswordFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager): void
    {
        $this->setUpAbstractCommandHandlerServices();
        $this->passwordService();
        $this->adapter();
        $this->config();
    }

    /**
     * @return PasswordService|m\MockInterface
     */
    protected function passwordService(): m\MockInterface
    {
        if (! $this->serviceManager->has(PasswordService::class)) {
            $this->serviceManager->setService(
                PasswordService::class,
                $this->setUpMockService(PasswordService::class)
            );
        }

        return $this->serviceManager->get(PasswordService::class);
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

    /**
     * @return array
     */
    protected function config(): array
    {
        if (! $this->serviceManager->has('Config')) {
            $this->serviceManager->setService(
                'Config',
                []
            );
        }

        return $this->serviceManager->get('Config');
    }
}
