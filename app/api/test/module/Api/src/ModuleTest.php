<?php

namespace OlcsTest\Api;

use Dvsa\Olcs\Api\Module as Sut;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use Zend\EventManager\Event;
use Zend\Mvc\Application;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\ServiceManager\ServiceManager;
use ZfcRbac\Service\AuthorizationService;

/**
 * Tests the Api Module php
 */
class ModuleTest extends MockeryTestCase
{
    public function testOnBootstrap()
    {
        $loginId = 123;

        $sut = m::mock(Sut::class)->makePartial();

        $mockShm = m::mock();
        $mockShm->shouldReceive('attach')->once()
            ->with(
                'Zend\Mvc\SendResponseListener',
                SendResponseEvent::EVENT_SEND_RESPONSE,
                m::type('callable')
            );

        $mockEm = m::mock();
        $mockEm->shouldReceive('getSharedManager')->andReturn($mockShm);

        $mockPvl = m::mock();
        $mockPvl->shouldReceive('attach')->with($mockEm, 1)->once();

        $mockAuth = m::mock(AuthorizationService::class);
        $mockAuth->shouldReceive('getIdentity->getUser->getLoginId')->once()->andReturn($loginId);

        $mockLog = m::mock();
        $mockLog->shouldReceive('get')->with(\Olcs\Logging\Log\Processor\UserId::class)->once()->andReturnSelf();
        $mockLog->shouldReceive('setUserId')->with($loginId)->once();

        $mockSm = m::mock(ServiceManager::class);
        $mockSm->shouldReceive('get')->with('PayloadValidationListener')->andReturn($mockPvl);
        $mockSm->shouldReceive('get')->with(AuthorizationService::class)->andReturn($mockAuth);
        $mockSm->shouldReceive('get')->with('LogProcessorManager')->andReturn($mockLog);

        $mockApp = m::mock(Application::class);
        $mockApp->shouldReceive('getServiceManager')->andReturn($mockSm);
        $mockApp->shouldReceive('getEventManager')->andReturn($mockEm);

        $mockEvent = m::mock(Event::class);
        $mockEvent->shouldReceive('getApplication')->andReturn($mockApp);

        $sut->onBootstrap($mockEvent);

    }

    public function testLogResponseHttp()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Module::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Zend\Http\PhpEnvironment\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('CONTENT');
        $mockRespone->shouldReceive('getStatusCode')->with()->twice()->andReturn(200);

        $sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Zend\Log\Logger::DEBUG, $logWriter->events[0]['priority']);
        $this->assertSame('API Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(['status' => 200, 'content' => 'CONTENT'], $logWriter->events[0]['extra']);
    }

    public function testLogResponseHttpEmpty()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Module::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Zend\Http\PhpEnvironment\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('');
        $mockRespone->shouldReceive('getStatusCode')->with()->andReturn(200);

        $sut->logResponse($mockRespone);

        $this->assertCount(2, $logWriter->events);
        $this->assertSame(\Zend\Log\Logger::ERR, $logWriter->events[0]['priority']);
        $this->assertSame('API Response is empty', $logWriter->events[0]['message']);
    }

    public function testLogResponseHttpEmpty204()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Module::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Zend\Http\PhpEnvironment\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('');
        $mockRespone->shouldReceive('getStatusCode')->with()->andReturn(204);

        $sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertNotContains('API Response is empty', $logWriter->events[0]);
    }

    public function testLogResponseCli()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Module::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Zend\Console\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('CONTENT');
        $mockRespone->shouldReceive('getErrorLevel')->with()->twice()->andReturn(0);

        $sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Zend\Log\Logger::DEBUG, $logWriter->events[0]['priority']);
        $this->assertSame('CLI Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(['errorLevel' => 0, 'content' => 'CONTENT'], $logWriter->events[0]['extra']);
    }

    public function testLogResponseCliError()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Module::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Zend\Console\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('CONTENT');
        $mockRespone->shouldReceive('getErrorLevel')->with()->twice()->andReturn(1);

        $sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Zend\Log\Logger::ERR, $logWriter->events[0]['priority']);
        $this->assertSame('CLI Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(['errorLevel' => 1, 'content' => 'CONTENT'], $logWriter->events[0]['extra']);
    }

    public function testLogResponseContentLong()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Module::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $logWriter = $this->setupLogger();

        $content = str_repeat('X', 1010);

        $mockRespone = m::mock(\Zend\Http\PhpEnvironment\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn($content);
        $mockRespone->shouldReceive('getStatusCode')->with()->twice()->andReturn(200);

        $sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Zend\Log\Logger::DEBUG, $logWriter->events[0]['priority']);
        $this->assertSame('API Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(str_repeat('X', 1000) .'...', $logWriter->events[0]['extra']['content']);
    }

    /**
     * Setup the logger, return the mock writer
     *
     * @return \Zend\Log\Writer\Mock
     */
    private function setupLogger()
    {
        $logWriter = new \Zend\Log\Writer\Mock();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($logWriter);
        Logger::setLogger($logger);

        return $logWriter;
    }
}
