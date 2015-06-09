<?php

/**
 * TmEmploymentTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\TmEmployment as Repo;
use Mockery as m;

/**
 * TmEmploymentTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TmEmploymentTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByTransportManager()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('te')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('contactDetails', 'cd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('cd.address')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('te.transportManager', ':tmId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('tmId', 534)->once();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchByTransportManager(534));
    }
}
