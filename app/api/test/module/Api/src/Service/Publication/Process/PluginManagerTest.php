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
        $this->sut = new PluginManager();
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->expectException(
            \Zend\ServiceManager\Exception\RuntimeException::class,
            'stdClass should implement: ' . ProcessInterface::class
        );

        //  call
        $this->sut->validatePlugin($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(ProcessInterface::class);
        // make sure no exception is thrown
        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
