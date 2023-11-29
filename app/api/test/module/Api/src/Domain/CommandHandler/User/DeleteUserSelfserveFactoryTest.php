<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\DeleteUserSelfserve;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\DeleteUserSelfserveFactory;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Dvsa\OlcsTest\Api\Domain\Repository\MocksUserRepositoryTrait;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * @see DeleteUserSelfserveFactory
 */
class DeleteUserSelfserveFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;
    use MocksUserRepositoryTrait;

    protected DeleteUserSelfserveFactory $sut;

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new DeleteUserSelfserveFactory();
    }

    public function testServiceCreated(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, DeleteUserSelfserve::class);

        // Assert
        $this->assertInstanceOf(TransactioningCommandHandler::class, $result);
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
