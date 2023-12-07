<?php

namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Cli\Controller\SQSController;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\CompanyProfile;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\CompanyProfileDlq;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvency;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvencyDlq;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Hamcrest\Core\IsEqual;

class SQSControllerTest extends MockeryTestCase
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
            'runFor' => 0.01, // seconds
            'sleepFor' => 50, // microseconds
        ]
    ];

    protected $mockCommandHandlerManager;

    public function setUp(): void
    {
        $this->request = m::mock('Laminas\Console\Request');

        $this->routeMatch = new RouteMatch([]);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $sm = m::mock(ServiceManager::class);

        $sm->shouldReceive('setService')
            ->andReturnUsing(
                function ($alias, $service) use ($sm) {
                    $sm->shouldReceive('get')->with($alias)->andReturn($service);
                    $sm->shouldReceive('has')->with($alias)->andReturn(true);
                    return $sm;
                }
            );

        $this->sm = $sm;
        $this->mockQueryHandlerManager = m::mock(QueryHandlerManager::class);
        $this->mockCommandHandlerManager = m::mock(CommandHandlerManager::class);
        $this->console = m::mock('Laminas\Console\Adapter\AdapterInterface');

        $this->sut = new SQSController($this->config, $this->mockQueryHandlerManager, $this->mockCommandHandlerManager);
        $this->sut->setEvent($this->event);
        $this->sut->setConsole($this->console);
    }

    /**
     * @dataProvider queueTypeCommandProvider
     */
    public function testIndexActionEmptyQueue($queueType, $expectedCommand)
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
        $this->request->shouldReceive('getParam')->with('queue')->andReturn($queueType);
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $result = new Result();
        $result->setFlag('no_messages', true);
        $this->mockCommandHandlerManager
            ->shouldReceive('handleCommand')
            ->with(IsEqual::equalTo($expectedCommand))
            ->andReturn($result)
            ->getMock();

        $this->sm->setService('CommandHandlerManager', $this->mockCommandHandlerManager);

        $this->console->shouldReceive('writeLine')->with('Queue type = ' . $queueType)->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->with('No messages queued, waiting for messages');

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $this->sut->dispatch($this->request);
    }

    /**
     * @return array
     */
    public function queueTypeCommandProvider()
    {
        return [
            ['companyProfile', CompanyProfile::create([])],
            ['processInsolvency', ProcessInsolvency::create([])],
            ['processInsolvencyDlq', ProcessInsolvencyDlq::create([])],
            ['companyProfileDlq', CompanyProfileDlq::create([])]
        ];
    }

    /**
     * @dataProvider queueTypeCommandProvider
     */
    public function testIndexAction($queueType, $expectedCommand)
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
        $this->request->shouldReceive('getParam')->with('queue')->andReturn($queueType);
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $result = new Result();
        $result->addMessage('Email sent');
        $this->mockCommandHandlerManager
            ->shouldReceive('handleCommand')
            ->with(IsEqual::equalTo($expectedCommand))
            ->andReturn($result)
            ->getMock();

        $this->sm->setService('CommandHandlerManager', $this->mockCommandHandlerManager);

        $this->console->shouldReceive('writeLine')->with('Queue type = ' . $queueType)->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->with('Processed message: Email sent');

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $response = $this->sut->dispatch($this->request);
        $this->assertEquals($response->getErrorLevel(), 0);
    }

    /**
     * @dataProvider queueTypeCommandProvider
     */
    public function testIndexActionHandlesException($queueType, $expectedCommand)
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
        $this->request->shouldReceive('getParam')->with('queue')->andReturn($queueType);
        $this->request->shouldReceive('getParam')->with('queue-duration', 0.01)->andReturn(0.01);

        $result = new Result();
        $result->addMessage('Email sent');
        $this->mockCommandHandlerManager
            ->shouldReceive('handleCommand')
            ->with(IsEqual::equalTo($expectedCommand))
            ->andThrows(new Exception('Something terrible happened'))
            ->getMock();

        $this->sm->setService('CommandHandlerManager', $this->mockCommandHandlerManager);

        $this->console->shouldReceive('writeLine')->with('Queue type = ' . $queueType)->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->with('Error: Something terrible happened');

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $this->sut->dispatch($this->request);
    }
}
