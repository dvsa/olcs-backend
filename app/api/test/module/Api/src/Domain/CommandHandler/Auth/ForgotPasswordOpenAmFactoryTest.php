<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ForgotPasswordOpenAm;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\ForgotPasswordOpenAmFactory;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Dvsa\OlcsTest\Api\Domain\Repository\MocksUserRepositoryTrait;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use ZfcRbac\Service\AuthorizationService;

/**
 * @see ForgotPasswordOpenAmFactory
 */
class ForgotPasswordOpenAmFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;
    use MocksUserRepositoryTrait;

    protected ForgotPasswordOpenAmFactory $sut;

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new ForgotPasswordOpenAmFactory();
    }

    public function testServiceCreated(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->pluginManager(), ForgotPasswordOpenAm::class);

        // Assert
        $this->assertInstanceOf(ForgotPasswordOpenAm::class, $result);
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager): void
    {
        $this->setUpAbstractCommandHandlerServices();
        $this->getMockService(ValidatableAdapterInterface::class);
        $this->getMockService(AuthorizationService::class);
        $this->userRepository();
    }
}
