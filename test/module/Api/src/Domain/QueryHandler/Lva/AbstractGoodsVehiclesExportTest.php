<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Lva;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Lva\AbstractGoodsVehiclesExport;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Lva\AbstractGoodsVehiclesExport
 */
class AbstractGoodsVehiclesExportTest extends QueryHandlerTestCase
{
    /** @var  DummyAbstractGoodsVehiclesExport */
    protected $sut;

    public function setUp()
    {
        $this->sut = new DummyAbstractGoodsVehiclesExport();

        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        parent::setUp();
    }

    public function testGetData()
    {
        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $mockLicVhclEntity = m::mock(Entity\Licence\LicenceVehicle::class)
            ->shouldReceive('serialize')
            ->twice()
            ->with(['vehicle', 'goodsDiscs', 'interimApplication'])
            ->andReturn('SERIALIZED')
            ->getMock();

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($qb, Query::HYDRATE_OBJECT)
            ->andReturn([$mockLicVhclEntity, clone $mockLicVhclEntity]);

        //  call & check
        $actual = $this->sut->getData($qb);

        static::assertEquals(
            [
                'results' => [
                    'SERIALIZED',
                    'SERIALIZED',
                ],
                'count' => 2,
            ],
            $actual
        );
    }
}

class DummyAbstractGoodsVehiclesExport extends AbstractGoodsVehiclesExport
{
    public function getData(QueryBuilder $qb)
    {
        return parent::getData($qb);
    }

    public function handleQuery(QueryInterface $query)
    {
    }
}
