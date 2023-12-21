<?php

namespace OlcsTest\Api;

use Dvsa\Olcs\Api\Module;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use phpseclib3\Crypt\AES;
use Laminas\EventManager\Event;
use Laminas\Mvc\Application;
use Laminas\Mvc\ResponseSender\SendResponseEvent;
use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Types\EncryptedStringType;

/**
 * Tests the Api Module php
 */
class ModuleTest extends MockeryTestCase
{
    /** @var  Module */
    private $sut;

    public function setUp(): void
    {
        $this->sut = m::mock(Module::class)->makePartial()->shouldAllowMockingProtectedMethods();
    }

    public function testOnBootstrap()
    {
        $loginId = 123;

        $mockShm = m::mock();
        $mockShm->shouldReceive('attach')->once()
            ->with(
                'Laminas\Mvc\SendResponseListener',
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
        $mockSm->shouldReceive('get')->with('config')->andReturn([]);

        $mockApp = m::mock(Application::class);
        $mockApp->shouldReceive('getServiceManager')->andReturn($mockSm);
        $mockApp->shouldReceive('getEventManager')->andReturn($mockEm);

        $mockEvent = m::mock(Event::class);
        $mockEvent->shouldReceive('getApplication')->andReturn($mockApp);

        $this->sut->onBootstrap($mockEvent);
    }

    public function testLogResponseHttp()
    {
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Laminas\Http\PhpEnvironment\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('CONTENT');
        $mockRespone->shouldReceive('getStatusCode')->with()->twice()->andReturn(200);

        $this->sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Laminas\Log\Logger::DEBUG, $logWriter->events[0]['priority']);
        $this->assertSame('API Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(['status' => 200, 'content' => 'CONTENT'], $logWriter->events[0]['extra']);
    }

    public function testLogResponseHttpEmptyOlcsDownloadHeader()
    {
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Laminas\Http\PhpEnvironment\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('');
        $mockRespone->shouldReceive('getStatusCode')->with()->andReturn(206);

        $this->sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertNotContains('API Response is empty', current($logWriter->events));
    }

    public function testLogResponseCli()
    {
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Laminas\Console\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('CONTENT');
        $mockRespone->shouldReceive('getErrorLevel')->with()->twice()->andReturn(0);

        $this->sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Laminas\Log\Logger::DEBUG, $logWriter->events[0]['priority']);
        $this->assertSame('CLI Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(['errorLevel' => 0, 'content' => 'CONTENT'], $logWriter->events[0]['extra']);
    }

    public function testLogResponseCliError()
    {
        $logWriter = $this->setupLogger();

        $mockRespone = m::mock(\Laminas\Console\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn('CONTENT');
        $mockRespone->shouldReceive('getErrorLevel')->with()->twice()->andReturn(1);

        $this->sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Laminas\Log\Logger::ERR, $logWriter->events[0]['priority']);
        $this->assertSame('CLI Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(['errorLevel' => 1, 'content' => 'CONTENT'], $logWriter->events[0]['extra']);
    }

    public function testLogResponseContentLong()
    {
        $logWriter = $this->setupLogger();

        $content = str_repeat('X', 1010);

        $mockRespone = m::mock(\Laminas\Http\PhpEnvironment\Response::class);
        $mockRespone->shouldReceive('getContent')->with()->once()->andReturn($content);
        $mockRespone->shouldReceive('getStatusCode')->with()->twice()->andReturn(200);

        $this->sut->logResponse($mockRespone);

        $this->assertCount(1, $logWriter->events);
        $this->assertSame(\Laminas\Log\Logger::DEBUG, $logWriter->events[0]['priority']);
        $this->assertSame('API Response Sent', $logWriter->events[0]['message']);
        $this->assertSame(str_repeat('X', 1000) . '...', $logWriter->events[0]['extra']['content']);
    }

    public function testInitDoctrineEncrypterType()
    {
        if (!EncryptedStringType::hasType(EncryptedStringType::TYPE)) {
            EncryptedStringType::addType(EncryptedStringType::TYPE, EncryptedStringType::class);
        }
        $this->sut->initDoctrineEncrypterType(['olcs-doctrine' => ['encryption_key' => 'ASaoW9TQogBu7TgDHoBKtsDPY5BdjF7W']]);

        /** @var AES $ciper */
        $ciper = \Doctrine\DBAL\Types\Type::getType('encrypted_string')->getEncrypter();

        $this->assertInstanceOf(AES::class, $ciper);
    }

    /**
     * Setup the logger, return the mock writer
     *
     * @return \Laminas\Log\Writer\Mock
     */
    private function setupLogger()
    {
        $logWriter = new \Laminas\Log\Writer\Mock();
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($logWriter);
        Logger::setLogger($logger);

        return $logWriter;
    }
}
