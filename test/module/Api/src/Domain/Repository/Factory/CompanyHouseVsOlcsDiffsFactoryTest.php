<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseVsOlcsDiffs;
use Dvsa\Olcs\Api\Domain\Repository\Factory\CompaniesHouseVsOlcsDiffsFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers Dvsa\Olcs\Api\Domain\Repository\Factory\CompaniesHouseVsOlcsDiffsFactory
 */
class CompanyHouseVsOlcsDiffsFactoryTest extends MockeryTestCase
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

        static::assertInstanceOf(
            CompaniesHouseVsOlcsDiffs::class,
            (new CompaniesHouseVsOlcsDiffsFactory())->createService($mockSl)
        );
    }
}
