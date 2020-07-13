<?php

/**
 * Validator Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\ValidatorManager;
use Zend\ServiceManager\ConfigInterface;

/**
 * Validator Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidatorManagerTest extends MockeryTestCase
{
    /**
     * @var ValidatorManager
     */
    protected $sut;

    public function setUp(): void
    {
        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')->with(m::type(ValidatorManager::class));

        $this->sut = new ValidatorManager($config);
    }

    public function testGet()
    {
        $mock = m::mock();

        $this->sut->setService('Foo', $mock);

        $this->assertSame($mock, $this->sut->get('Foo'));
    }
}
