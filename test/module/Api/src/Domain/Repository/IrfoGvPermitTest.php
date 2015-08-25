<?php

/**
 * IrfoGvPermit Repo test
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermit as Repo;

/**
 * IrfoGvPermit Repo test
 */
class IrfoGvPermitTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchById()
    {
        $id = 24;
        $mockResult = [0 => 'result'];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefData')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('irfoGvPermitType')
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with($id);

        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($mockResult);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(IrfoGvPermit::class)
            ->andReturn($repo);

        $result = $this->sut->fetchById($id);

        $this->assertEquals($result, $mockResult[0]);
    }

    public function testFetchByOrganisation()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByOrganisation('ORG1'));

        $expectedQuery = 'BLAH AND m.organisation = [[ORG1]]';
        $this->assertEquals($expectedQuery, $this->query);

    }
}
