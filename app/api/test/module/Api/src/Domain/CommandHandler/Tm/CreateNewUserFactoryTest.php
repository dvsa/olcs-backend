<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\CreateNewUser;
use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\CreateNewUserFactory;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\OlcsTest\MocksServicesTrait;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Class CreateNewUserFactoryTest
 * @see CreateNewUserFactory
 */
class CreateNewUserFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;

    /**
     * @var CreateNewUserFactory
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
        $this->assertIsCallable([$this->sut, '__invoke']);
    }


    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeReturnsWrappedCreateNewUserCommandHandler(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager, CreateNewUser::class);

        // Assert
        $this->assertInstanceOf(CreateNewUser::class, $result->getWrapped());
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new CreateNewUserFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
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
