<?php

/**
 * LicenceOperatingCentreTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\LicenceOperatingCentre as Repo;
use Mockery as m;

/**
 * LicenceOperatingCentreTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceOperatingCentreTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByLicence()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('loc')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('operatingCentre', 'oc')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('oc.address')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('loc.licence', ':licenceId')->once()->andReturn('EXPR');
        $mockQb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licenceId', 7634)->once();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchByLicence(7634));
    }

    public function testFetchByLicenceIdForOperatingCentres()
    {
        $qb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getArrayResult')
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchByLicenceIdForOperatingCentres(111));

        $expected = implode(
            ' ',
            [
                '{QUERY}',
                'LEFT JOIN loc.s4 s4',
                'INNER JOIN loc.operatingCentre oc',
                'INNER JOIN oc.address oca',
                'LEFT JOIN oca.countryCode ocac',
                'LEFT JOIN oc.complaints occ WITH occ.status = [[ecst_open]]',
                'AND loc.licence = [[111]]',
                'SELECT s4',
                'SELECT oc',
                'SELECT oca',
                'SELECT ocac',
                'SELECT occ',
                'ORDER BY oca.id ASC'
            ]
        );

        $this->assertEquals($expected, $this->query);
    }
}
