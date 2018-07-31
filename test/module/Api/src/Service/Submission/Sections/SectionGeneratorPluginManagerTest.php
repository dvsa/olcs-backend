<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorInterface;
use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ConfigInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager
 */
class SectionGeneratorPluginManagerTest extends MockeryTestCase
{
    /** @var  SectionGeneratorPluginManager */
    private $sut;

    public function setUp()
    {
        $mockCfg = m::mock(ContainerInterface::class);

        $this->sut = new SectionGeneratorPluginManager($mockCfg);
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->expectException(\Zend\ServiceManager\Exception\InvalidServiceException::class);

        //  call
        $this->sut->validate($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(SectionGeneratorInterface::class);
        $this->sut->validate($plugin);
    }
}
