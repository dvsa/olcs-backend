<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\OlcsTest\Api\Service\Stub\AbstractServiceManagerFactoryStub;
use Dvsa\OlcsTest\Api\Service\Stub\ServiceManagerStub;
use Psr\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory
 */
class AbstractServiceManagerFactoryTest extends MockeryTestCase
{
    public function testInvoke()
    {
        $mockSl = m::mock(ContainerInterface::class)
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        'config' => [
                            AbstractServiceManagerFactoryStub::CONFIG_KEY => ['cfg_data'],
                        ]
                    ];

                    return $map[$class];
                }
            )
            ->getMock();

        $actual = (new AbstractServiceManagerFactoryStub())->__invoke($mockSl, ServiceManagerStub::class);

        static::assertInstanceOf(ServiceManagerStub::class, $actual);
    }
}
