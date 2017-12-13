<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Factory;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Factory\DataDvaNiFactory;
use Dvsa\Olcs\Api\Domain\Repository\DataDvaNi;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers  Dvsa\Olcs\Api\Domain\Repository\Factory\DataDvaNiFactory
 */
class DataDvaNiFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockConn = m::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('close')
            ->getMock();

        $mockSl = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->once()
            ->with('doctrine.connection.export')
            ->andReturn($mockConn)
            ->getMock();

        /** @var ServiceManager $mockSm */
        $mockSm = m::mock(ServiceManager::class)
            ->shouldReceive('getServiceLocator')
            ->once()
            ->andReturn($mockSl)
            ->getMock();

        static::assertInstanceOf(
            DataDvaNi::class,
            (new DataDvaNiFactory())->createService($mockSm)
        );
    }
}
