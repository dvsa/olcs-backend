<?php

/**
 * Query Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Query Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryHandlerTestCase extends MockeryTestCase
{
    /**
     * @var QueryHandlerInterface
     */
    protected $sut;

    /**
     * @var QueryHandlerManager
     */
    protected $queryHandler;

    /**
     * @var ServiceLocatorInterface
     */
    protected $repoManager;

    protected $repoMap = [];

    protected $mockedSmServices = [];

    public function setUp()
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class)->makePartial();

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager->setService($alias, $service);
        }

        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->setService('RepositoryServiceManager', $this->repoManager);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->setService($serviceName, $service);
        }

        $this->queryHandler = m::mock(QueryHandlerManager::class)->makePartial();
        $this->queryHandler->setServiceLocator($sm);

        $this->sut = $this->sut->createService($this->queryHandler);
    }

    protected function mockRepo($name, $class)
    {
        $this->repoMap[$name] = m::mock($class);
    }
}
