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
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;
use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

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
        $config = m::mock(ConfigInterface::class);

        $config->shouldReceive('configureServiceManager')
            ->with(m::type(MessageConsumerManager::class))
            ->once();

        new MessageConsumerManager($config);
    }

    public function testValidate()
    {
        $plugin = m::mock(MessageConsumerInterface::class);

        $this->assertNull($this->sut->validate($plugin));
    }

    public function testValidateInvalid()
    {
        $this->expectException(InvalidServiceException::class);

        $this->sut->validate(null);
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePlugin()
    {
        $plugin = m::mock(MessageConsumerInterface::class);

        $this->assertNull($this->sut->validatePlugin($plugin));
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePluginInvalid()
    {
        $this->expectException(RuntimeException::class);

        $this->sut->validatePlugin(null);
    }
}
