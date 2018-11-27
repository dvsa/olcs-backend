<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Data\Container;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Container  test
 */
class ContainerTest extends TestCase
{
    public function testLogger()
    {
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $container = new Container($logger);
        $this->assertSame($logger, $container->getLogger());
    }

    public function testGenerateId()
    {
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $container = new Container($logger);

        $num1 = $container->generateId();
        usleep(100);
        $num2 = $container->generateId();

        $this->assertStringStartsWith('_', $num1);
        $this->assertNotEquals($num1, $num2);
    }

    public function testDebugMessageDisabled()
    {
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $container = new Container($logger);
        $this->assertNull($container->debugMessage('FOO', 'BAR'));
    }

    public function testDebugMessageEnabled()
    {
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $logger->shouldReceive('debug')->with('BAR - FOO')->once();
        $container = new Container($logger);
        $container->setDebugLog($logger);
        $this->assertNull($container->debugMessage('FOO', 'BAR'));
    }

    public function testDebugMessageEnabledObject()
    {
        $obj = new \StdClass();
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $logger->shouldReceive('debug')->with('BAR - stdClass')->once();
        $container = new Container($logger);
        $container->setDebugLog($logger);
        $this->assertNull($container->debugMessage($obj, 'BAR'));
    }

    public function testPostRedirect()
    {
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $container = new Container($logger);
        $this->assertNull($container->postRedirect('URL'));
    }

    public function testRedirect()
    {
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $container = new Container($logger);
        $this->assertNull($container->redirect('URL'));
    }
}
