<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\Repository\ReadonlyRepositoryInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
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

    public function testValidateException(): void
    {
        $invalidClass = new \stdClass();
        $message = sprintf(
            RepositoryServiceManager::VALIDATE_ERROR,
            RepositoryServiceManager::class,
            get_class($invalidClass)
        );
        $this->expectException(InvalidServiceException::class);
        $this->expectExceptionMessage($message);
        $this->sut->validate($invalidClass);
    }

    /** @dataProvider dpValidate */
    public function testValidate($instance): void
    {
        $this->assertNull($this->sut->validate(m::mock($instance)));
    }

    public function dpValidate(): array
    {
        return [
            [RepositoryInterface::class],
            [ReadonlyRepositoryInterface::class],
        ];
    }
}
