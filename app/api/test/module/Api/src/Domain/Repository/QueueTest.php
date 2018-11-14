<?php

/**
 * Queue test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\Queue as QueueRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Mockery as m;

/**
 * Queue test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class QueueTest extends RepositoryTestCase
{
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\Queue
     */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(QueueRepo::class, true);
    }

    public function testGetNextItem()
    {
        $item = m::mock(QueueEntity::class)->makePartial();

        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([$item])
                ->getMock()
        );

        $ref = m::mock(RefData::class)->makePartial();
        $this->sut->shouldReceive('getRefdataReference')
            ->with(QueueEntity::STATUS_PROCESSING)
            ->once()
            ->andReturn($ref);
        $this->sut
            ->shouldReceive('save')
            ->with($item)
            ->once();

        $this->assertEquals($item, $this->sut->getNextItem());

        $now = new DateTime();
        $expectedQuery = '[QUERY] AND q.status = [[que_sts_queued]] AND '.
            '(q.processAfterDate <= [['. $now->format(DateTime::W3C) .']] OR q.processAfterDate IS NULL) LIMIT 1';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testGetNextItemInclude()
    {
        $item = m::mock(QueueEntity::class)->makePartial();

        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([$item])
                ->getMock()
        );

        $ref = m::mock(RefData::class)->makePartial();
        $this->sut->shouldReceive('getRefdataReference')
            ->with(QueueEntity::STATUS_PROCESSING)
            ->once()
            ->andReturn($ref);
        $this->sut
            ->shouldReceive('save')
            ->with($item)
            ->once();

        $this->assertEquals($item, $this->sut->getNextItem(['foo']));

        $now = new DateTime();
        $expectedQuery = '[QUERY] AND q.status = [[que_sts_queued]] AND'.
            ' (q.processAfterDate <= [['. $now->format(DateTime::W3C) .']] OR q.processAfterDate IS NULL) LIMIT 1'.
            ' AND q.type IN [[["foo"]]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testGetNextItemExclude()
    {
        $item = m::mock(QueueEntity::class)->makePartial();

        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([$item])
                ->getMock()
        );

        $ref = m::mock(RefData::class)->makePartial();
        $this->sut->shouldReceive('getRefdataReference')
            ->with(QueueEntity::STATUS_PROCESSING)
            ->once()
            ->andReturn($ref);
        $this->sut
            ->shouldReceive('save')
            ->with($item)
            ->once();

        $this->assertEquals($item, $this->sut->getNextItem(['foo'], ['bar']));

        $now = new DateTime();
        $expectedQuery = '[QUERY] AND q.status = [[que_sts_queued]] AND'.
            ' (q.processAfterDate <= [['. $now->format(DateTime::W3C) .']] OR q.processAfterDate IS NULL) LIMIT 1'.
            ' AND q.type IN [[["foo"]]] AND q.type NOT IN [[["bar"]]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testEnqueueContinuationNotSought()
    {
        $options1 = '{"id":1,"version":2}';
        $options2 = '{"id":3,"version":4}';

        $query = 'INSERT INTO `queue` (`status`, `type`, `options`) VALUES '
            . '(:status1, :type1, :options1), (:status2, :type2, :options2)';

        $params = [
            'status1' => QueueEntity::STATUS_QUEUED,
            'type1' => QueueEntity::TYPE_CNS,
            'options1' => $options1,
            'status2' => QueueEntity::STATUS_QUEUED,
            'type2' => QueueEntity::TYPE_CNS,
            'options2' => $options2
        ];

        $mockStatement = m::mock()
            ->shouldReceive('execute')
            ->with($params)
            ->once()
            ->shouldReceive('rowCount')
            ->andReturn(2)
            ->once()
            ->getMock();

        $mockConnection = m::mock()
            ->shouldReceive('prepare')
            ->with($query)
            ->andReturn($mockStatement)
            ->once()
            ->getMock();

        $this->em->shouldReceive('getConnection')
            ->andReturn($mockConnection)
            ->once()
            ->getMock();

        $licences = [
            ['id' => 1, 'version' => 2],
            ['id' => 3, 'version' => 4]
        ];

        $this->assertEquals(2, $this->sut->enqueueContinuationNotSought($licences));
    }

    public function testIsItemTypeQueuedTrue()
    {
        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getArrayResult')->with()->once()->andReturn(['X']);

        $this->assertTrue($this->sut->isItemTypeQueued('foo'));

        $now = new DateTime();
        $expectedQuery = '[QUERY] AND q.status = [[que_sts_queued]] AND'.
            ' (q.processAfterDate <= [['. $now->format(DateTime::W3C) .']] OR q.processAfterDate IS NULL) LIMIT 1'.
            ' AND q.type = [[foo]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testIsItemTypeQueuedFalse()
    {
        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getArrayResult')->with()->once()->andReturn([]);

        $this->assertFalse($this->sut->isItemTypeQueued('foo'));

        $now = new DateTime();
        $expectedQuery = '[QUERY] AND q.status = [[que_sts_queued]] AND'.
            ' (q.processAfterDate <= [['. $now->format(DateTime::W3C) .']] OR q.processAfterDate IS NULL) LIMIT 1'.
            ' AND q.type = [[foo]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testEnqueueAllOrganisations()
    {
        $mockStatement = m::mock();
        $mockConnection = m::mock();
        $expectedSql = <<<SQL
INSERT INTO `queue` (`status`, `type`, `options`, `created_by`, `last_modified_by`, `created_on`)
SELECT DISTINCT 'que_sts_queued',
                ?,
                CONCAT('{"companyNumber":"', UPPER(o.company_or_llp_no), '"}'),
                ?,
                ?,
                NOW()
FROM organisation o
INNER JOIN licence l ON o.id=l.organisation_id
WHERE l.status IN ('lsts_consideration',
                   'lsts_suspended',
                   'lsts_valid',
                   'lsts_curtailed',
                   'lsts_granted')
  AND o.company_or_llp_no IS NOT NULL
  AND o.type IN ('org_t_rc', 'org_t_llp')
ORDER BY o.company_or_llp_no;
SQL;
        $this->em->shouldReceive('getConnection')->with()->once()->andReturn($mockConnection);
        $mockConnection->shouldReceive('prepare')->with($expectedSql)->once()->andReturn($mockStatement);
        $mockStatement->shouldReceive('execute')
            ->with(['TYPE', PidIdentityProvider::SYSTEM_USER, PidIdentityProvider::SYSTEM_USER])
            ->once()->andReturnNull();
        $mockStatement->shouldReceive('rowCount')->with()->once()->andReturn(99);

        $this->sut->enqueueAllOrganisations('TYPE');
    }

    public function testFetchNextItemIncludingPostponedWithIncludeTypes()
    {
        $item = m::mock(QueueEntity::class)->makePartial();

        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('q.processAfterDate', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([$item])
                ->getMock()
        );

        $this->assertEquals($item, $this->sut->fetchNextItemIncludingPostponed(['foo']));

        $expectedQuery = '[QUERY] AND q.status = [[que_sts_queued]] LIMIT 1'.
            ' AND q.type IN [[["foo"]]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchNextItemIncludingPostponedWithexcludeTypes()
    {
        $item = m::mock(QueueEntity::class)->makePartial();

        $qb = $this->createMockQb('[QUERY]');
        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('q.processAfterDate', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([$item])
                ->getMock()
        );

        $this->assertEquals($item, $this->sut->fetchNextItemIncludingPostponed(['foo'], ['bar']));
        $expectedQuery = '[QUERY] AND q.status = [[que_sts_queued]] LIMIT 1'.
            ' AND q.type IN [[["foo"]]] AND q.type NOT IN [[["bar"]]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    /**
     * @param array $results
     * @param bool  $expected
     *
     * @dataProvider dpIsItemInQueue
     */
    public function testIsItemInQueue($results, $expected)
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getArrayResult')
                ->andReturn($results)
                ->getMock()
        );
        $this->assertEquals($expected, $this->sut->isItemInQueue(['T1', 'T2'], ['S1', 'S2']));

        $expectedQuery = 'BLAH '
            . 'SELECT q.id '
            . 'AND q.type IN [[["T1","T2"]]] '
            . 'AND q.status IN [[["S1","S2"]]] '
            . 'LIMIT 1';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function dpIsItemInQueue()
    {
        return [
            'exists in the queue' => [['RESULTS'], true],
            'not in the queue' => [[], false],
        ];
    }
}
