<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\GoodsVehiclesExport;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Licence\GoodsVehiclesExport
 */
class GoodsVehiclesExportTest extends QueryHandlerTestCase
{
    const ID = 1111;
    const LICENCE_ID = 777;

    /** @var GoodsVehiclesExport|m\MockInterface */
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock(GoodsVehiclesExport::class . '[getData]')
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query\Licence\GoodsVehiclesExport::create(['id' => self::ID]);

        /** @var QueryBuilder $mockListQb */
        $mockListQb = m::mock(QueryBuilder::class);

        $mockLicEntity = m::mock(Entity\Application\Application::class)
            ->shouldReceive('getId')->once()->andReturn(self::LICENCE_ID)
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockLicEntity);

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('createPaginatedVehiclesDataForLicenceQuery')
            ->with($query, self::LICENCE_ID)
            ->andReturn($mockListQb);

        $this->sut
            ->shouldReceive('getData')
            ->once()
            ->with($mockListQb)
            ->andReturn('EXPECT_RESULT');

        //  call & check
        $actual = $this->sut->handleQuery($query);

        static::assertEquals('EXPECT_RESULT', $actual);
    }
}
