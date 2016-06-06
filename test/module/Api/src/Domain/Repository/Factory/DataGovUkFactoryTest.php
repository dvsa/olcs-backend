<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Factory;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Factory\DataGovUkFactory;
use Dvsa\Olcs\Api\Domain\Repository\DataGovUk;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers  Dvsa\Olcs\Api\Domain\Repository\Factory\DataGovUkFactory
 */
class DataGovUkFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockSl = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->once()
            ->with('doctrine.connection.export')
            ->andReturn(m::mock(\Doctrine\DBAL\Connection::class))
            ->getMock();

        /** @var ServiceManager $mockSm */
        $mockSm = m::mock(ServiceManager::class)
            ->shouldReceive('getServiceLocator')
            ->once()
            ->andReturn($mockSl)
            ->getMock();

        static::assertInstanceOf(
            DataGovUk::class,
            (new DataGovUkFactory())->createService($mockSm)
        );
    }
}
