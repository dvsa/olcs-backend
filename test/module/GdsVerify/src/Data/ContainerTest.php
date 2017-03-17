<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Data\Container;
use Mockery as m;

/**
 * Container  test
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
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

        $this->assertStringStartsWith('GdsVerify', $num1);
        $this->assertNotEquals($num1, $num2);
    }

    public function testDebugMessage()
    {
        $logger = m::mock(\Psr\Log\LoggerInterface::class);
        $container = new Container($logger);
        $this->assertNull($container->debugMessage('FOO', 'BAR'));
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
