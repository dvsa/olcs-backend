<?php

/**
 * OtherLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\OtherLicence as Repo;
use Mockery as m;

/**
 * OtherLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OtherLicenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByTransportManager()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('ol')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('ol.transportManager', ':tmId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('tmId', 834)->once();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchByTransportManager(834));
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $mockQ = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);
        $mockQ->shouldReceive('getTransportManager')->with()->twice()->andReturn(33);

        $mockQb->shouldReceive('expr->eq')->with('ol.transportManager', ':tmId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('tmId', 33)->once();

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
