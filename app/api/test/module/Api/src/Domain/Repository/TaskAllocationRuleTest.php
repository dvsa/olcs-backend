<?php

/**
 * Task Allocation Rule Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
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
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TaskAllocationRuleTest extends RepositoryTestCase
{
    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->setUpSut(TaskAllocationRuleRepo::class);
    }

    /**
     * Test fetch by parameters
     *
     * @dataProvider paramProvider
     * @param int $category
     * @param string $operatorType
     * @param string $trafficArea
     * @param bool $isMlh
     * @param string $query
     */
    public function testFetchByParameters($category, $operatorType, $trafficArea, $isMlh, $query)
    {
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

        $this->assertEquals(
            'RESULT',
            $this->sut->fetchByParameters($category, $operatorType, $trafficArea, $isMlh)
        );
        $this->assertEquals(
            $query,
            $this->query
        );
    }

    /**
     * Param provider
     *
     * @return array
     */
    public function paramProvider()
    {
        return [
            // category, operatorType, trafficArea, isMlh, query
            [
                111,
                'gv',
                'B',
                1,
                '[QUERY] AND m.category = [[111]] AND m.goodsOrPsv = [[gv]] AND m.trafficArea = [[B]] ' .
                'AND m.isMlh = [[1]]'
            ],
            [
                111,
                'gv',
                'B',
                null,
                '[QUERY] AND m.category = [[111]] AND m.goodsOrPsv = [[gv]] AND m.trafficArea = [[B]] ' .
                'AND m.isMlh IS NULL'
            ],
            [
                111,
                'gv',
                null,
                null,
                '[QUERY] AND m.category = [[111]] AND m.goodsOrPsv = [[gv]] AND m.trafficArea IS NULL ' .
                'AND m.isMlh IS NULL'
            ],
            [
                111,
                null,
                null,
                null,
                '[QUERY] AND m.category = [[111]] AND m.goodsOrPsv IS NULL AND m.trafficArea IS NULL ' .
                'AND m.isMlh IS NULL'
            ],
        ];
    }

    /**
     * Test build default list query
     */
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
