<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Repository\Correspondence
 */
class CorrespondenceTest extends RepositoryTestCase
{
    /** @var  Repository\Correspondence */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(Repository\Correspondence::class, true);
    }

    public function testApplyListMethods()
    {
        $orgId = 9999;

        $mockQb = $this->createMockQb('{{QUERY}}');

        $mockQry = m::mock(TransferQry\Correspondence\Correspondences::class)
            ->shouldReceive('getOrganisation')->once()->andReturn($orgId)
            ->getMock();

        $this->sut->applyListJoins($mockQb);
        $this->sut->applyListFilters($mockQb, $mockQry);

        static::assertEquals(
            '{{QUERY}} ' .
            'SELECT l, d ' .
            'INNER JOIN co.licence l ' .
            'INNER JOIN co.document d ' .
            'AND l.organisation = [[' . $orgId . ']] ' .
            'ORDER BY co.createdOn DESC',
            $this->query
        );
    }

    public function testGetUnreadCountForOrganisation()
    {
        $organisationId = 123;

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('co')
            ->once()
            ->andReturn($mockQb);

        $mockQb
            ->shouldReceive('select')
            ->once()
            ->with('COUNT(co)')
            ->andReturnSelf();

        $organisationCondition = m::mock();
        $accessedCondition = m::mock();
        $mockQb->shouldReceive('expr->eq')
            ->with('l.organisation', ':organisationId')
            ->once()
            ->andReturn($organisationCondition);
        $mockQb->shouldReceive('expr->eq')
            ->with('co.accessed', ':accessed')
            ->once()
            ->andReturn($accessedCondition);
        $mockQb->shouldReceive('join')
            ->with('co.licence', 'l', 'WITH', $organisationCondition)
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with($accessedCondition)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(':organisationId', $organisationId)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(':accessed', 'N');

        $mockQb->shouldReceive('getQuery->getSingleScalarResult')->once()->andReturn(22);

        $this->assertEquals(22, $this->sut->getUnreadCountForOrganisation($organisationId));
    }
}
