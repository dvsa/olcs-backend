<?php

/**
 * Licence test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Doctrine\DBAL\LockMode;

/**
 * Licence test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(LicenceRepo::class);
    }

    public function testFetchSafetyDetailsUsingId()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn(null);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('workshops', 'w')
            ->andReturnSelf()
            ->shouldReceive('withContactDetails')
            ->once();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Licence::class)
            ->andReturn($repo);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchSafetyDetailsUsingId($command, Query::HYDRATE_OBJECT, 1);
    }

    public function testFetchSafetyDetailsUsingIdWithResults()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        $result = m::mock(Licence::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('workshops', 'w')
            ->andReturnSelf()
            ->shouldReceive('withContactDetails')
            ->once();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Licence::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchSafetyDetailsUsingId($command, Query::HYDRATE_OBJECT, 1);
    }

    public function testFetchByLicNo()
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Licence::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('with')->with('operatingCentres', 'ocs')->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('ocs.operatingCentre', 'ocs_oc')->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('ocs_oc.address', 'ocs_oc_a')->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.licNo', ':licNo')->once()->andReturn('EXPR');
        $qb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licNo', 'LIC0001')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULTS']);

        $this->assertSame('RESULTS', $this->sut->fetchByLicNo('LIC0001'));
    }

    public function testFetchByLicNoNotFound()
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Licence::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('with')->with('operatingCentres', 'ocs')->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('ocs.operatingCentre', 'ocs_oc')->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('ocs_oc.address', 'ocs_oc_a')->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.licNo', ':licNo')->once()->andReturn('EXPR');
        $qb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licNo', 'LIC0001')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn([]);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchByLicNo('LIC0001');
    }

    public function testFetchByVrm()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByVrm('ABC123'));

        $expectedQuery = '[QUERY] INNER JOIN m.licenceVehicles lv INNER JOIN lv.vehicle v'
            . ' AND lv.removalDate IS NULL AND v.vrm = [[ABC123]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchWithEnforcementArea()
    {
        $licenceId = 1;

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('getQuery->getSingleResult')
            ->andReturn('RESULT');

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('enforcementArea')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($licenceId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Licence::class)
            ->andReturn($repo);

        $result = $this->sut->fetchWithEnforcementArea($licenceId);
        $this->assertEquals('RESULT', $result);
    }

    public function testFetchWithOperatingCentres()
    {
        $licenceId = 1;

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('getQuery->getSingleResult')
            ->andReturn('RESULT');

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('operatingCentres', 'oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('oc.operatingCentre', 'oc_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('oc_oc.address', 'oc_oc_a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($licenceId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Licence::class)
            ->andReturn($repo);

        $result = $this->sut->fetchWithOperatingCentres($licenceId);
        $this->assertEquals('RESULT', $result);
    }

    public function testFetchWithPrivateHireLicence()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('privateHireLicences', 'phl')->once()->andReturnSelf()
            ->shouldReceive('with')->with('phl.contactDetails', 'cd')->once()->andReturnSelf()
            ->shouldReceive('with')->with('cd.address', 'add')->once()->andReturnSelf()
            ->shouldReceive('with')->with('add.countryCode')->once()->andReturnSelf()
            ->shouldReceive('byId')->with(21)->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getSingleResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchWithPrivateHireLicence(21));

        $expectedQuery = 'BLAH';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
