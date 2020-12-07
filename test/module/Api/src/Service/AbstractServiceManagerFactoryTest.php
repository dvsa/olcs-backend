<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\OlcsTest\Api\Service\Stub\AbstractServiceManagerFactoryStub;
use Dvsa\OlcsTest\Api\Service\Stub\ServiceManagerStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory
 */
class AbstractServiceManagerFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        /** @var  \Laminas\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        'Config' => [
                            AbstractServiceManagerFactoryStub::CONFIG_KEY => ['cfg_data'],
                        ]
                    ];

                    return $map[$class];
                }
            )
            ->getMock();

        $actual = (new AbstractServiceManagerFactoryStub())->createService($mockSl);

        static::assertInstanceOf(ServiceManagerStub::class, $actual);
    }
}
