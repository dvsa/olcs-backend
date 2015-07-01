<?php

/**
 * TrafficArea test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;

/**
 * TrafficArea test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrafficAreaTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(TrafficAreaRepo::class);
    }

    public function testFetchUsingIdWithResults()
    {
        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->once()
            ->andReturn($mockQb);

        $ta1 = m::mock(TrafficArea::class)->makePartial()
            ->setId('N')
            ->setName('Norn Iron');
        $ta2 = m::mock(TrafficArea::class)->makePartial()
            ->setId('B')
            ->setName('Area B');
        $ta3 = m::mock(TrafficArea::class)->makePartial()
            ->setId('A')
            ->setName('Area A');

        $results = [$ta1, $ta2, $ta3];

        $mockQb->shouldReceive('getQuery->getResult')
            ->andReturn($results);

        $valueOptions = $this->sut->getValueOptions();

        $this->assertEquals(
            [
                'A' => 'Area A',
                'B' => 'Area B',
            ],
            $valueOptions
        );
    }
}
