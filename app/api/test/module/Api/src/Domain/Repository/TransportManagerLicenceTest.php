<?php

/**
 * TransportManagerLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as Repo;
use Mockery as m;

/**
 * TransportManagerLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerLicenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchWithContactDetailsByLicence()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('tml')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $mockQb->shouldReceive('join')->with('tml.transportManager', 'tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('addSelect')->with('tm')->once()->andReturnSelf();
        $mockQb->shouldReceive('join')->with('tm.homeCd', 'hcd')->once()->andReturnSelf();
        $mockQb->shouldReceive('addSelect')->with('hcd')->once()->andReturnSelf();
        $mockQb->shouldReceive('join')->with('hcd.person', 'p')->once()->andReturnSelf();
        $mockQb->shouldReceive('addSelect')->with('p')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('tml.licence', ':licenceId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licenceId', 834)->once();
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchWithContactDetailsByLicence(834));
    }
}
