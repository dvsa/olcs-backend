<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorInterface;
use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ConfigInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager
 */
class SectionGeneratorPluginManagerTest extends MockeryTestCase
{
    /** @var  SectionGeneratorPluginManager */
    private $sut;

    public function setUp(): void
    {
        $mockCfg = m::mock(ConfigInterface::class)
            ->shouldReceive('configureServiceManager')
            ->getMock();

        $this->sut = new SectionGeneratorPluginManager($mockCfg);
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->expectException(
            \Laminas\ServiceManager\Exception\RuntimeException::class,
            'stdClass should implement: ' . SectionGeneratorInterface::class
        );

        //  call
        $this->sut->validatePlugin($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(SectionGeneratorInterface::class);
        $this->sut->validatePlugin($plugin);
    }
}
