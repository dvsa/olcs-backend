<?php

namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Cli\Service\Queue\QueueProcessor;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Cli\Controller\QueueController;
use OlcsTest\Bootstrap;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;

/**
 * Queue Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueueControllerTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
    protected $console;
    protected $config = [
        'queue' => [
            'runFor' => 0.01,
            'sleepFor' => 50,
            ]
    ];
    protected $mockQueueService;

    public function setUp(): void
    {
        $this->request = m::mock('Laminas\Console\Request');

        $this->routeMatch = new RouteMatch([]);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->sm = Bootstrap::getServiceManager();
        $this->console = m::mock('Laminas\Console\Adapter\AdapterInterface');

        $this->mockQueueService = m::mock(QueueProcessor::class);

        $this->sut = new QueueController(
            $this->config,
            $this->mockQueueService,
            m::mock(QueryHandlerManager::class),
            m::mock(CommandHandlerManager::class)
        );
        $this->sut->setEvent($this->event);
        $this->sut->setConsole($this->console);
    }

    public function testIndexActionEmptyQueue()
    {
        // Mocks
        $mockConfig = [
            'queue' => [
                'runFor' => 0.01, // seconds
                'sleepFor' => 50, // microseconds
            ]
        ];
        $mockQueue = m::mock();
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $this->mockQueueService->shouldReceive('processNextItem')
            ->with(['foo'], [])
            ->andReturn(null);

        $this->console->shouldReceive('writeLine')->with('Types = foo')->once();
        $this->console->shouldReceive('writeLine')->with('Exclude types = ')->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->with('No items queued, waiting for items');

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $this->sut->dispatch($this->request);
    }

    public function testIndexAction()
    {
        $mockQueue = m::mock();
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $this->mockQueueService->shouldReceive('processNextItem')
            ->with(['foo'], [])
            ->andReturn('Some output');

        $this->console->shouldReceive('writeLine')->with('Types = foo')->once();
        $this->console->shouldReceive('writeLine')->with('Exclude types = ')->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->atLeast(100)
            ->with('Some output');

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $response = $this->sut->dispatch($this->request);
        $this->assertEquals($response->getErrorLevel(), 0);
    }

    public function testIndexActionIncludeExclude()
    {
        $mockQueue = m::mock();
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo,bar');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('aaa,bbb');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $this->mockQueueService->shouldReceive('processNextItem')
            ->with(['foo', 'bar'], ['aaa', 'bbb'])
            ->andReturn('Some output');

        $this->console->shouldReceive('writeLine')->with('Types = foo,bar')->once();
        $this->console->shouldReceive('writeLine')->with('Exclude types = aaa,bbb')->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->atLeast(100)
            ->with('Some output');

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $this->sut->dispatch($this->request);
    }

    public function testIndexActionHandlesException()
    {
        $mockQueue = m::mock();
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $errorMessage = 'error message';
        $this->mockQueueService->shouldReceive('processNextItem')
            ->with(['foo'], [])
            ->andThrow(new \Exception($errorMessage));

        $this->console->shouldReceive('writeLine')->with('Types = foo')->once();
        $this->console->shouldReceive('writeLine')->with('Exclude types = ')->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->atLeast(1)
            ->with('Error: ' . $errorMessage);

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $this->sut->dispatch($this->request);
    }

    public function testIndexActionHandlesOrmException()
    {
        $mockQueue = m::mock();
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $errorMessage = 'error message';
        $this->mockQueueService->shouldReceive('processNextItem')
            ->with(['foo'], [])
            ->andThrow(new \Doctrine\ORM\ORMException($errorMessage));

        $this->console->shouldReceive('writeLine')->with('Types = foo')->once();
        $this->console->shouldReceive('writeLine')->with('Exclude types = ')->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->atLeast(1)
            ->with('ORM Error: ' . $errorMessage);

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        /** @var \Laminas\Mvc\Console\View\ViewModel $model */
        $model = $this->sut->dispatch($this->request);

        $this->assertEquals(1, $model->getErrorLevel());
    }
}
