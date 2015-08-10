<?php

/**
 * ApplicationOperatingCentreTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre as Repo;
use Dvsa\Olcs\Api\Entity;
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

    public function testFetchByS4()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('aoc')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('aoc.s4', ':s4Id')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('s4Id', 12)->once();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchByS4(12));
    }

    public function testFetchByApplicationIdForOperatingCentres()
    {
        $qb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getArrayResult')
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchByApplicationIdForOperatingCentres(111));

        $expected = implode(
            ' ',
            [
                '{QUERY}',
                'INNER JOIN aoc.operatingCentre oc',
                'INNER JOIN oc.address oca',
                'LEFT JOIN oca.countryCode ocac',
                'LEFT JOIN oc.complaints occ WITH occ.status = [[ecst_open]]',
                'AND aoc.application = [[111]]',
                'SELECT oc',
                'SELECT oca',
                'SELECT ocac',
                'SELECT occ',
                'ORDER BY oca.id ASC'
            ]
        );

        $this->assertEquals($expected, $this->query);
    }

    public function testFindCorrespondingLoc()
    {
        /** @var Entity\OperatingCentre\OperatingCentre $oc */
        $oc = m::mock(Entity\OperatingCentre\OperatingCentre::class)->makePartial();

        /** @var Entity\Application\ApplicationOperatingCentre $aoc */
        $aoc = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc->setOperatingCentre($oc);

        /** @var Entity\Licence\LicenceOperatingCentre $loc */
        $loc = m::mock(Entity\Licence\LicenceOperatingCentre::class)->makePartial();
        $loc->setOperatingCentre($oc);

        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        $foundLoc = $this->sut->findCorrespondingLoc($aoc, $licence);

        $this->assertSame($loc, $foundLoc);
    }

    public function testFindCorrespondingLocWithoutMatch()
    {
        /** @var Entity\OperatingCentre\OperatingCentre $oc */
        $oc = m::mock(Entity\OperatingCentre\OperatingCentre::class)->makePartial();

        /** @var Entity\Application\ApplicationOperatingCentre $aoc */
        $aoc = m::mock(Entity\Application\ApplicationOperatingCentre::class)->makePartial();
        $aoc->setOperatingCentre($oc);

        /** @var Entity\Licence\LicenceOperatingCentre $loc */
        $loc = m::mock(Entity\Licence\LicenceOperatingCentre::class)->makePartial();

        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        $this->setExpectedException(\Exception::class);

        $this->sut->findCorrespondingLoc($aoc, $licence);
    }
}
