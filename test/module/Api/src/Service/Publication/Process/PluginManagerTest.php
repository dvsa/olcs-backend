<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ConfigInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Process\PluginManager
 */
class PluginManagerTest extends MockeryTestCase
{
    /** @var  PluginManager */
    private $sut;

    public function setUp()
    {
        $mockCfg = m::mock(ConfigInterface::class)
            ->shouldReceive('configureServiceManager')
            ->getMock();

        $this->sut = new PluginManager($mockCfg);
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->setExpectedException(
            \Zend\ServiceManager\Exception\RuntimeException::class,
            'stdClass should implement: ' . ProcessInterface::class
        );

        //  call
        $this->sut->validatePlugin($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(ProcessInterface::class);
        $this->sut->validatePlugin($plugin);
    }
}
