<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Rbac\IdentityProviderInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\ServiceManager\ServiceManager;
use Mockery\MockInterface;
use Mockery as m;
use Dvsa\OlcsTest\MocksRepositoriesTrait;

trait MocksAbstractCommandHandlerServicesTrait
{
    use MocksRepositoriesTrait;

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
     * @return IdentityProviderInterface|MockInterface
     */
    protected function pidIdentityProvider(): MockInterface
    {
        if (! $this->serviceManager()->has(IdentityProviderInterface::class)) {
            $this->serviceManager()->setService(
                IdentityProviderInterface::class,
                $this->setUpMockService(IdentityProviderInterface::class)
            );
        }
        return $this->serviceManager()->get(IdentityProviderInterface::class);
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
