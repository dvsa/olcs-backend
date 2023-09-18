<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager as ProcessPluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\Exception\InvalidServiceException;

/**
 * @covers ProcessPluginManager
 */
class PluginManagerTest extends MockeryTestCase
{
    private ProcessPluginManager $sut;

    public function setUp(): void
    {
        $this->sut = new ProcessPluginManager();
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
}
