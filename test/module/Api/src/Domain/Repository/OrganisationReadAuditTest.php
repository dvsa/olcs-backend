<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationReadAudit;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationReadAudit as OrganisationReadAuditEntity;
use Dvsa\Olcs\Transfer\Query\Audit\ReadOrganisation;
use Mockery as m;

/**
 * Organisation Read Audit Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationReadAuditTest extends RepositoryTestCase
{
    /** @var OrganisationReadAudit|m\MockInterface */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(OrganisationReadAudit::class, true);
    }

    public function testFetchOne()
    {
        $userId = 111;
        $entityId = 222;

        $qb = $this->createMockQb('{{QUERY}}');
        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getOneOrNullResult')->andReturn(['foo']);

        static::assertEquals(['foo'], $this->sut->fetchOne($userId, $entityId));

        $expected = '{{QUERY}} AND m.user = [[111]] AND m.organisation = [[222]] AND m.createdOn >= CURRENT_DATE()';

        static::assertEquals($expected, $this->query);
    }

    public function testFetchList()
    {
        $queryDto = ReadOrganisation::create(['id' => 111]);

        $qb = $this->createMockQb('{{QUERY}}');
        $this->mockCreateQueryBuilder($qb);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn(['result']);

        $qbh = m::mock();
        $qbh->shouldReceive('withRefdata')->once();
        $qbh->shouldReceive('paginate')->once();

        $this->queryBuilder->shouldReceive('modifyQuery')->andReturn($qbh);

        static::assertEquals(['result'], $this->sut->fetchList($queryDto, Query::HYDRATE_OBJECT));

        $expected = '{{QUERY}} INNER JOIN m.user u INNER JOIN u.contactDetails cd '
            . 'INNER JOIN cd.person p AND m.organisation = [[111]] ORDER BY m.createdOn DESC';

        static::assertEquals($expected, $this->query);
    }

    public function testDeleteOlderThan()
    {
        $query = m::mock();
        $query->shouldReceive('setParameter')->once()->with('oldestDate', '2015-01-01');
        $query->shouldReceive('execute')->once()->andReturn(10);

        $this->em->shouldReceive('createQuery')
            ->once()
            ->with('DELETE FROM ' . OrganisationReadAuditEntity::class . ' e WHERE e.createdOn <= :oldestDate')
            ->andReturn($query);

        $result = $this->sut->deleteOlderThan('2015-01-01');

        static::assertEquals(10, $result);
    }
}
