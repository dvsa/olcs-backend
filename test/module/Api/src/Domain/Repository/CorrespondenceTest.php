<?php

/**
 * Correspondence Repo test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Correspondence as Repo;

/**
 * Correspondence Repo test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CorrespondenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class, true);
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
