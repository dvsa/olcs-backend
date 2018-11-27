<?php

/**
 * Validation Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\ValidationHandlerManager;
use Zend\ServiceManager\ConfigInterface;

/**
 * Validation Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidationHandlerManagerTest extends MockeryTestCase
{
    /**
     * @var ValidationHandlerManager
     */
    protected $sut;

    public function setUp()
    {
        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')->with(m::type(ValidationHandlerManager::class));

        $this->sut = new ValidationHandlerManager($config);
    }

    public function testGet()
    {
        $mock = m::mock(HandlerInterface::class);

        $this->sut->setService('Foo', $mock);

        $this->assertSame($mock, $this->sut->get('Foo'));
    }

    public function testGetInvalid()
    {
        $this->expectException(\Zend\ServiceManager\Exception\RuntimeException::class);

        $this->sut->setService('Foo', m::mock());

        $this->sut->get('Foo');
    }
}
