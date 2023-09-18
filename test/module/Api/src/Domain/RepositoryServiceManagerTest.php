<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RepositoryServiceManagerTest extends MockeryTestCase
{
    protected RepositoryServiceManager $sut;

    public function setUp(): void
    {
        $container = m::mock(ContainerInterface::class);
        $this->sut = new RepositoryServiceManager($container, []);
    }

    public function testValidate(): void
    {
        $this->assertNull($this->sut->validate(m::mock(RepositoryInterface::class)));
    }

}
