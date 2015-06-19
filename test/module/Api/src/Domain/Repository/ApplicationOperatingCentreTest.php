<?php

/**
 * ApplicationOperatingCentreTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre as Repo;
use Mockery as m;

/**
 * ApplicationOperatingCentreTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationOperatingCentreTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByApplication()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('aoc')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('operatingCentre', 'oc')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('oc.address')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('aoc.application', ':applicationId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('applicationId', 12)->once();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchByApplication(12));
    }
}
