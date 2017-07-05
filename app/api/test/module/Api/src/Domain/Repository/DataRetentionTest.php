<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\DataRetention;
use Mockery as m;

/**
 * Class DataRetentionTest
 */
class DataRetentionTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(DataRetention::class);
    }

    public function testFetchEntitiesToDelete()
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('with')->with('dataRetentionRule', 'drr')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['FOO']);

        $result = $this->sut->fetchEntitiesToDelete(12);

        $this->assertSame(['FOO'], $result);

        $expectedQuery = '[QUERY] AND drr.isEnabled = 1 AND m.toAction = 1 AND m.actionConfirmation = 1 AND '.
            'm.actionedDate IS NULL AND m.nextReviewDate IS NULL LIMIT 12';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
