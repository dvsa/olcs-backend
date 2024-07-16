<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\CommandHandler\User\CreateUserFactory;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Dvsa\OlcsTest\MocksServicesTrait;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\CreateUser;
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class CreateUserFactoryTest
 * @see CreateUserFactory
 */
class CreateUserFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;

    /**
     * @var CreateUserFactory
     */
    protected $sut;

    /**
     * @test
     */
    public function invokeIsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable($this->sut->__invoke(...));
    }


    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeReturnsWrappedCreateUserCommandHandler(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, CreateUser::class);

        // Assert
        $this->assertInstanceOf(CreateUser::class, $result->getWrapped());
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new CreateUserFactory();
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager): void
    {
        $this->setUpAbstractCommandHandlerServices();
        $serviceManager->setService(AuthorizationService::class, $this->setUpMockService(AuthorizationService::class));
        $serviceManager->setService(ValidatableAdapterInterface::class, $this->setUpMockService(ValidatableAdapterInterface::class));
        $serviceManager->setService(PasswordService::class, $this->setUpMockService(PasswordService::class));
        $this->setupRespositories();
    }

    private function setupRespositories()
    {
        $repositoryServiceManager = $this->serviceManager->get('RepositoryServiceManager');
        assert($repositoryServiceManager instanceof RepositoryServiceManager);
        $mockUserRepository = m::mock(User::class);
        $repositoryServiceManager->setService('User', $mockUserRepository);
    }
}
