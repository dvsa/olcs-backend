<?php

/**
 * Query Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Query Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryHandlerTestCase extends MockeryTestCase
{
    /**
     * @var AbstractQueryHandler
     */
    protected $sut;

    /**
     * @var QueryHandlerManager
     */
    protected $queryHandler;

    /**
     * @var m\MockInterface|ServiceLocatorInterface
     */
    protected $repoManager;

    /** @var m\MockInterface[]  */
    protected $repoMap = [];

    /** @var m\MockInterface[]  */
    protected $mockedSmServices = [];

    public function setUp()
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('CommandHandlerManager')->andReturn(null);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
        }

        // if not already mocked AuthorizationService then do it
        if (!isset($this->mockedSmServices[AuthorizationService::class])) {
            $sm->shouldReceive('get')->with(AuthorizationService::class)
                ->andReturn(
                    m::mock(AuthorizationService::class)->shouldReceive('isGranted')->andReturn(false)->getMock()
                );
        }

        $this->queryHandler = m::mock(QueryHandlerManager::class);
        $this->queryHandler
            ->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->sut = $this->sut->createService($this->queryHandler);
    }

    public function tearDown()
    {
        parent::tearDown();

        unset(
            $this->sut,
            $this->queryHandler,
            $this->repoManager,
            $this->repoMap,
            $this->mockedSmServices
        );
    }

    protected function mockRepo($name, $class)
    {
        if (!$class instanceof m\MockInterface) {
            $class = m::mock($class);
        }

        return $this->repoMap[$name] = $class;
    }
}
