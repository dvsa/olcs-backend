<?php
declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\ServiceManager\ServiceManager;
use Mockery\MockInterface;
use Mockery as m;

trait MocksAbstractCommandHandlerServicesTrait
{
    /**
     * @return ServiceManager
     */
    abstract protected function serviceManager(): ServiceManager;

    /**
     * @param string $class
     * @return mixed
     */
    abstract protected function setUpMockService(string $class);

    protected function setUpAbstractCommandHandlerServices()
    {
        $this->commandHandlerManager();
        $this->repositoryServiceManager();
        $this->updateOperatingCentreHelper();
        $this->queryHandlerManager();
        $this->pidIdentityProvider();
        $this->transactionManager();
        $this->cacheEncryption();
    }

    /**
     * @return CommandHandlerManager
     */
    protected function commandHandlerManager(): CommandHandlerManager
    {
        if (! $this->serviceManager()->has('CommandHandlerManager')) {
            $instance = $this->setUpCommandHandlerManager();
            $this->serviceManager()->setService('CommandHandlerManager', $instance);
        }
        return $this->serviceManager()->get('CommandHandlerManager');
    }

    /**
     * @return m\MockInterface|CommandHandlerManager
     */
    protected function setUpCommandHandlerManager(): m\MockInterface
    {
        $instance = m::mock(CommandHandlerManager::class)->makePartial();
        $instance->setServiceLocator($this->serviceManager());
        $instance->allows('handleCommand')->andReturnUsing(function () {
            return new Result();
        })->byDefault();
        return $instance;
    }

    /**
     * @return RepositoryServiceManager
     */
    protected function repositoryServiceManager(): RepositoryServiceManager
    {
        if (! $this->serviceManager()->has('RepositoryServiceManager')) {
            $instance = new RepositoryServiceManager();
            $this->serviceManager()->setService('RepositoryServiceManager', $instance);
            $this->setUpRepositories();
        }
        return $this->serviceManager()->get('RepositoryServiceManager');
    }

    protected function setUpRepositories(): void
    {
        // Register any repositories needed in the RepositoryServiceManager which is accessible through $this->serviceManager
    }

    /**
     * @return UpdateOperatingCentreHelper|MockInterface
     */
    protected function updateOperatingCentreHelper(): MockInterface
    {
        if (! $this->serviceManager()->has('UpdateOperatingCentreHelper')) {
            $this->serviceManager()->setService(
                'UpdateOperatingCentreHelper',
                $this->setUpMockService(UpdateOperatingCentreHelper::class)
            );
        }

        return $this->serviceManager()->get('UpdateOperatingCentreHelper');
    }

    /**
     * @return QueryHandlerManager|MockInterface
     */
    protected function queryHandlerManager(): MockInterface
    {
        if (! $this->serviceManager()->has('QueryHandlerManager')) {
            $this->serviceManager()->setService(
                'QueryHandlerManager',
                $this->setUpMockService(QueryHandlerManager::class)
            );
        }

        return $this->serviceManager()->get('QueryHandlerManager');
    }

    /**
     * @return PidIdentityProvider|MockInterface
     */
    protected function pidIdentityProvider(): MockInterface
    {
        if (! $this->serviceManager()->has(PidIdentityProvider::class)) {
            $this->serviceManager()->setService(
                PidIdentityProvider::class,
                $this->setUpMockService(PidIdentityProvider::class)
            );
        }

        return $this->serviceManager()->get(PidIdentityProvider::class);
    }

    /**
     * @return TransactionManagerInterface|MockInterface
     */
    protected function transactionManager(): MockInterface
    {
        if (! $this->serviceManager()->has('TransactionManager')) {
            $this->serviceManager()->setService(
                'TransactionManager',
                $this->setUpMockService(TransactionManagerInterface::class)
            );
        }

        return $this->serviceManager()->get('TransactionManager');
    }

    /**
     * @return CacheEncryption|MockInterface
     */
    protected function cacheEncryption(): MockInterface
    {
        if (! $this->serviceManager()->has(CacheEncryption::class)) {
            $this->serviceManager()->setService(
                CacheEncryption::class,
                $this->setUpMockService(CacheEncryption::class)
            );
        }

        return $this->serviceManager()->get(CacheEncryption::class);
    }
}
