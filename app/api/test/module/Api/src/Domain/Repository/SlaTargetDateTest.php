<?php

/**
 * SlaTargetDate Repo Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;
use Doctrine\ORM\QueryBuilder;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * SlaTargetDate Repo Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SlaTargetDateTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repository\SlaTargetDate::class);
    }

    public function testFetchUsingEntityIdAndType()
    {
        $entityType = 'document';
        $entityId = 100;

        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getSingleResult')->once()->andReturn('foobar');

        $result = $this->sut->fetchUsingEntityIdAndType($entityType, $entityId);

        $this->assertEquals('QUERY AND m.document = [[100]]', $this->query);

        $this->assertEquals('foobar', $result);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repository\SlaTargetDate::class);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('document', 100)
            ->andReturnSelf();

        $mockQ = m::mock(QueryInterface::class);
        $mockQ->shouldReceive('getEntityType')
            ->andReturn('document')
            ->shouldReceive('getEntityId')
            ->andReturn(100);

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
