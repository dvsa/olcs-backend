<?php

namespace Dvsa\OlcsTest\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Cli\Controller\SQSController;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\CompanyProfile;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\CompanyProfileDlq;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvency;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvencyDlq;
use Exception;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
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

    public function setUp()
    {
        $this->request = m::mock('Zend\Console\Request');

        $this->routeMatch = new RouteMatch([]);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->sm = Bootstrap::getServiceManager();
        $this->console = m::mock('Zend\Console\Adapter\AdapterInterface');

        $this->sut = new SQSController();
        $this->sut->setEvent($this->event);
        $this->sut->setServiceLocator($this->sm);
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
        $mockCommandHandlerManager = m::mock(CommandHandlerManager::class)
            ->shouldReceive('handleCommand')
            ->with(IsEqual::equalTo($expectedCommand))
            ->andReturn($result)
            ->getMock();

        $this->sm->setService('CommandHandlerManager', $mockCommandHandlerManager);

        $this->console->shouldReceive('writeLine')->with('Queue type = '. $queueType)->once();
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
        $mockCommandHandlerManager = m::mock(CommandHandlerManager::class)
            ->shouldReceive('handleCommand')
            ->with(IsEqual::equalTo($expectedCommand))
            ->andReturn($result)
            ->getMock();

        $this->sm->setService('CommandHandlerManager', $mockCommandHandlerManager);

        $this->console->shouldReceive('writeLine')->with('Queue type = '. $queueType)->once();
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
        $mockCommandHandlerManager = m::mock(CommandHandlerManager::class)
            ->shouldReceive('handleCommand')
            ->with(IsEqual::equalTo($expectedCommand))
            ->andThrows(new Exception('Something terrible happened'))
            ->getMock();

        $this->sm->setService('CommandHandlerManager', $mockCommandHandlerManager);

        $this->console->shouldReceive('writeLine')->with('Queue type = '. $queueType)->once();
        $this->console->shouldReceive('writeLine')->with('Queue duration = 0.01')->once();
        $this->console->shouldReceive('writeLine')
            ->with('Error: Something terrible happened');

        // Assertions
        $this->routeMatch->setParam('action', 'index');
        $this->sut->dispatch($this->request);
    }
}
