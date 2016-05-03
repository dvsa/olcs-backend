<?php

namespace OlcsTest\Api;

use Olcs\Logging\Log\Logger;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the Api Module php
 */
class ModuleTest extends MockeryTestCase
{
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
        $mockRespone->shouldReceive('getStatusCode')->with()->twice()->andReturn(200);

        $sut->logResponse($mockRespone);

        $this->assertCount(2, $logWriter->events);
        $this->assertSame(\Zend\Log\Logger::ERR, $logWriter->events[0]['priority']);
        $this->assertSame('API Response is empty', $logWriter->events[0]['message']);
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
