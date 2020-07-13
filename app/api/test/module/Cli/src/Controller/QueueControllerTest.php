<?php

/**
 * Queue Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Controller;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Cli\Controller\QueueController;
use OlcsTest\Bootstrap;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

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

    public function setUp(): void
    {
        $this->request = m::mock('Zend\Console\Request');

        $this->routeMatch = new RouteMatch([]);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->sm = Bootstrap::getServiceManager();
        $this->console = m::mock('Zend\Console\Adapter\AdapterInterface');

        $this->sut = new QueueController();
        $this->sut->setEvent($this->event);
        $this->sut->setServiceLocator($this->sm);
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
        $this->sm->setService('Config', $mockConfig);
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $mockQueue->shouldReceive('processNextItem')
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
        // Mocks
        $mockConfig = [
            'queue' => [
                'runFor' => 0.01,
                'sleepFor' => 50,
            ]
        ];
        $mockQueue = m::mock();
        $this->sm->setService('Config', $mockConfig);
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $mockQueue->shouldReceive('processNextItem')
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
        // Mocks
        $mockConfig = [
            'queue' => [
                'runFor' => 0.01,
                'sleepFor' => 50,
            ]
        ];
        $mockQueue = m::mock();
        $this->sm->setService('Config', $mockConfig);
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo,bar');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('aaa,bbb');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $mockQueue->shouldReceive('processNextItem')
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

    public function testIndexActionQueueDuration()
    {
        // Mocks
        $mockConfig = [
            'queue' => [
                'runFor' => 22,
            ]
        ];
        $mockQueue = m::mock();
        $this->sm->setService('Config', $mockConfig);
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo,bar');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('aaa,bbb');
        $this->request->shouldReceive('getParam')->with('queue-duration', 22)->andReturn(0.01);

        $mockQueue->shouldReceive('processNextItem')
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
        // Mocks
        $mockConfig = [
            'queue' => [
                'runFor' => 0.01,
            ]
        ];
        $mockQueue = m::mock();
        $this->sm->setService('Config', $mockConfig);
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $errorMessage = 'error message';
        $mockQueue->shouldReceive('processNextItem')
            ->with(['foo'], [])
            ->andThrow(new \Exception($errorMessage));

        $this->console->shouldReceive('writeLine')->with('Types = foo')->once();
        $this->console->shouldReceive('writeLine')->with('Exclude types = ')->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->atLeast(1)
            ->with('Error: '.$errorMessage);

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $this->sut->dispatch($this->request);
    }

    public function testIndexActionHandlesOrmException()
    {
        // Mocks
        $mockConfig = [
            'queue' => [
                'runFor' => 0.01,
            ]
        ];
        $mockQueue = m::mock();
        $this->sm->setService('Config', $mockConfig);
        $this->sm->setService('Queue', $mockQueue);

        // Expectations
        $this->request->shouldReceive('getParam')->with('type')->andReturn('foo');
        $this->request->shouldReceive('getParam')->with('exclude')->andReturn('');
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $errorMessage = 'error message';
        $mockQueue->shouldReceive('processNextItem')
            ->with(['foo'], [])
            ->andThrow(new \Doctrine\ORM\ORMException($errorMessage));

        $this->console->shouldReceive('writeLine')->with('Types = foo')->once();
        $this->console->shouldReceive('writeLine')->with('Exclude types = ')->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->atLeast(1)
            ->with('ORM Error: '.$errorMessage);

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        /** @var \Zend\View\Model\ConsoleModel $model */
        $model = $this->sut->dispatch($this->request);

        $this->assertEquals(1, $model->getErrorLevel());
    }
}
