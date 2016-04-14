<?php

/**
 * Task Allocation Rule Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule as TaskAllocationRuleRepo;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;

/**
 * Task Allocation Rule Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskAllocationRuleTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(TaskAllocationRuleRepo::class);
    }

    public function testFetchForSimpleTaskAssignment()
    {
        $category = m::mock(Category::class)->makePartial();
        $category->setId(111);

        $qb = $this->createMockQb('[QUERY]');
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn('RESULT');

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Entity::class)
            ->andReturn($repo);

        $this->assertEquals('RESULT', $this->sut->fetchForSimpleTaskAssignment($category));

        $this->assertEquals(
            '[QUERY] AND m.category = [[111]] AND m.isMlh IS NULL AND m.trafficArea IS NULL',
            $this->query
        );
    }


    public function testBuildDefaultListQuery()
    {
        $qb = $this->createMockQb('[QUERY]');
        $query = m::mock(QueryInterface::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('category', 'cat')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('goodsOrPsv', 'gop')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('trafficArea', 'ta')->once()->andReturnSelf();

        $this->sut->buildDefaultListQuery($qb, $query);

        $this->assertSame(
            '[QUERY] SELECT cat.description as HIDDEN categoryDescription SELECT gop.id as HIDDEN criteria SELECT '
            . 'ta.name as HIDDEN trafficAreaName',
            $this->query
        );
    }
}
