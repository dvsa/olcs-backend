<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Laminas\ServiceManager\ServiceLocatorInterface;

abstract class CompaniesHouseConsumerTestCase extends CommandHandlerTestCase
{
    protected function setupService()
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->queryHandler = m::mock(QueryHandlerManager::class);
        $this->pidIdentityProvider = m::mock(PidIdentityProvider::class);
        $this->mockTransationMngr = m::mock(TransactionManagerInterface::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('TransactionManager')->andReturn($this->mockTransationMngr);
        $sm->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->queryHandler);
        $sm->shouldReceive('get')->with(PidIdentityProvider::class)->andReturn($this->pidIdentityProvider);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
        }

        $this->commandHandler = m::mock(CommandHandlerManager::class);
        $this->commandHandler
            ->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->sut->createService($this->commandHandler);
    }
}
