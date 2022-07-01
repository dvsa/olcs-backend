<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Process\PluginManager
 */
class PluginManagerTest extends MockeryTestCase
{
    /** @var  PluginManager */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new PluginManager();
    }

    public function testValidate()
    {
        $plugin = m::mock(ProcessInterface::class);

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
        $plugin = m::mock(ProcessInterface::class);

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
