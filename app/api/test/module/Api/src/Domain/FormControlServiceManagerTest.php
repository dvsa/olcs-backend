<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\FormControlServiceManager;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * FormControlServiceManagerTest
 */
class FormControlServiceManagerTest extends MockeryTestCase
{
    /**
     * @var FormControlServiceManager
     */
    protected $sut;

    public function setUp(): void
    {
        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')
            ->with(m::type(FormControlServiceManager::class))
            ->once();

        $this->sut = new FormControlServiceManager($config);
    }

    public function testValidate()
    {
        $plugin = m::mock(FormControlStrategyInterface::class);

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
        $plugin = m::mock(FormControlStrategyInterface::class);

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
