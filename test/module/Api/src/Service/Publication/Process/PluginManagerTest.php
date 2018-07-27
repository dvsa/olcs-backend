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
        /** @var  \Zend\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Config')
            ->andReturn(
                [
                    'publication_context' => [],
                ]
            )
            ->getMock();

        $this->sut = new PluginManager($mockSl);
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->setExpectedException(
            \Zend\ServiceManager\Exception\InvalidServiceException::class,
            'stdClass should implement: ' . ProcessInterface::class
        );

        //  call
        $this->sut->validate($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(ProcessInterface::class);
        $this->sut->validate($plugin);
    }
}
