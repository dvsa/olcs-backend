<?php

/**
 * Printer test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\PrintScan\Printer as PrinterEntity;
use Dvsa\Olcs\Transfer\Query\Printer\PrinterList as PrinterListQry;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Printer as PrinterRepo;

/**
 * Printer test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrinterTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(PrinterRepo::class, true);
    }

    public function testFetchWithTeams()
    {
        $id = 1;

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->once()
            ->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->with($id)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('teams')
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getSingleResult')->andReturn(['result']);

        $this->assertSame(['result'], $this->sut->fetchWithTeams($id));
    }
}
