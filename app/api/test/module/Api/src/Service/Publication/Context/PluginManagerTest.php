<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\ContextInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\PluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Context\PluginManager
 */
class PluginManagerTest extends MockeryTestCase
{
    /** @var  PluginManager */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new PluginManager();
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->expectException(
            \Laminas\ServiceManager\Exception\RuntimeException::class,
            'stdClass should implement: ' . ContextInterface::class
        );

        //  call
        $this->sut->validatePlugin($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(ContextInterface::class);
        // make sure no exception is thrown
        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
