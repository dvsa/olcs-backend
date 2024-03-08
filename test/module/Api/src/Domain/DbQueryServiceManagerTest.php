<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\Repository\Query\QueryInterface;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\DbQueryServiceManager;

class DbQueryServiceManagerTest extends MockeryTestCase
{
    private DbQueryServiceManager $sut;

    public function setUp(): void
    {
        $container = m::mock(ContainerInterface::class);
        $this->sut = new DbQueryServiceManager($container, []);
    }

    public function testGet()
    {
        $mock = m::mock(QueryInterface::class);
        $this->sut->setService('Foo', $mock);
        $this->assertSame($mock, $this->sut->get('Foo'));
    }

    public function testValidate()
    {
        $this->assertNull($this->sut->validate(m::mock(QueryInterface::class)));
    }

    public function testValidateInvalid()
    {
        $this->expectException(InvalidServiceException::class);
        $this->sut->validate(null);
    }
}
