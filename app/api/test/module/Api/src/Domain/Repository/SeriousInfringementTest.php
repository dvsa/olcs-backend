<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement as SiRepo;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Comparison;
use Mockery as m;

/**
 * SeriousInfringementTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SeriousInfringementTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(SiRepo::class, true);
    }

    public function testApplyListFilters()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\Cases\Si\GetList::create(['case' => 812]);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.case = [[812]]';

        $this->assertEquals($expectedQuery, $this->query);
    }

    /**
     * @dataProvider fetchByNotificationNumberProvider
     *
     * @param $results
     * @param $expectedReturnValue
     */
    public function testFetchByNotificationNo($results, $expectedReturnValue)
    {
        $notificationNumber = 9435839546;
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);
        $comparison = m::mock(Comparison::class);

        $this->em->shouldReceive('getRepository')->with(SiEntity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')
            ->with('m.notificationNumber', ':notificationNumber')
            ->once()
            ->andReturn($comparison);
        $qb->shouldReceive('where')->with($comparison)->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('notificationNumber', $notificationNumber)->once()->andReturnSelf();
        $qb->shouldReceive('setMaxResults')->with(1)->once()->andReturnSelf();
        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn($results);

        $this->assertSame($expectedReturnValue, $this->sut->fetchByNotificationNumber($notificationNumber));
    }

    /**
     * data provider for testFetchByNotificationNumber()
     *
     * @return array
     */
    public function fetchByNotificationNumberProvider()
    {
        return [
            [[], null],
            [[0 => 'result'], 'result']
        ];
    }
}
