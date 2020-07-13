<?php

/**
 * Message Consumer Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager;

/**
 * Message Consumer Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MessageConsumerManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new MessageConsumerManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testConstructWithConfig()
    {
        $config = m::mock('\Zend\ServiceManager\ConfigInterface');

        $config->shouldReceive('configureServiceManager')
            ->with(m::type('\Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager'));

        new MessageConsumerManager($config);
    }

    public function testInitializeWithoutInterface()
    {
        $instance = m::mock();
        $instance->shouldReceive('setServiceLocator')
            ->never();

        $this->sut->initialize($instance);
    }

    public function testInitializeWithInterface()
    {
        $instance = m::mock('\Zend\ServiceManager\ServiceLocatorAwareInterface');
        $instance->shouldReceive('setServiceLocator')
            ->once()
            ->with($this->sm);

        $this->sut->initialize($instance);
    }

    public function testValidatePluginInvalid()
    {
        $this->expectException('\Zend\ServiceManager\Exception\RuntimeException');

        $plugin = m::mock();

        $this->sut->validatePlugin($plugin);
    }

    public function testValidatePlugin()
    {
        $plugin = m::mock('\Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface');

        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
