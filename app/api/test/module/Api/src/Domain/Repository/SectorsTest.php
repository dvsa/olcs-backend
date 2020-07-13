<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\Sectors;
use Dvsa\Olcs\Api\Entity\Permits\Sectors as Entity;
use Mockery as m;

/**
 * Sectors test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SectorsTest extends RepositoryTestCase
{
    public function setUp(): void
    {
        $this->setUpSut(Sectors::class);
    }

    public function testFetchQaOptions()
    {
        $result = [
            [
                'value' => '1',
                'label' => 'Chemicals',
                'hint' => 'Chemical products, man-made fibres, rubber and plastic products, nuclear fuel'
            ],
            [
                'value' => '2',
                'label' => 'Food products',
                'hint' => 'Beverages and tobacco, products of agriculture, hunting and forests'
            ],
        ];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('s.id as value, s.name as label, s.description as hint')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(Entity::class, 's')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('s.displayOrder', 'ASC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getScalarResult')
            ->once()
            ->andReturn($result);

        $this->assertEquals(
            $result,
            $this->sut->fetchQaOptions()
        );
    }
}
