<?php

/**
 * Team repo test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Team as TeamRepo;

/**
 * Team repo test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TeamTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(TeamRepo::class);
    }

    public function testFetchByName()
    {
        $name = 'foo';

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->once()
            ->andReturn($mockQb);

        $mockQb->shouldReceive('expr->eq')->with('m.name', ':name')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('name', $name)->once();

        $mockQb->shouldReceive('getQuery->getResult')->andReturn(['result']);

        $this->assertSame(['result'], $this->sut->fetchByName($name));
    }
}
