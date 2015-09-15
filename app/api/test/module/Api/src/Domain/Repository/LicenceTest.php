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
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

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

    public function testApplyListFilters()
    {
        $this->setUpSut(LicenceRepo::class, true);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.organisation', ':organisation')->once()->andReturn('EXPR1');
        $mockQb->shouldReceive('setParameter')->with('organisation', 723)->once()->andReturn();
        $mockQb->shouldReceive('andWhere')->with('EXPR1')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->notIn')->with('m.status', ':excludeStatuses')->once()->andReturn('EXPR2');
        $mockQb->shouldReceive('setParameter')->with('excludeStatuses', ['status1', 'status2'])->once()->andReturn();
        $mockQb->shouldReceive('andWhere')->with('EXPR2')->once()->andReturnSelf();

        $mockQuery = m::mock(QueryInterface::class);
        $mockQuery->shouldReceive('getOrganisation')->with()->andReturn(723);
        $mockQuery->shouldReceive('getExcludeStatuses')->with()->andReturn(['status1', 'status2']);

        $this->sut->applyListFilters($mockQb, $mockQuery);
    }

    public function testFetchForContinuation()
    {
        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->once()->with($qb)->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('trafficArea', 'ta')->once()->andReturnSelf();

        $qb->shouldReceive('expr->gte')->with('m.expiryDate', ':expiryFrom')->once()->andReturn('condFrom');
        $qb->shouldReceive('andWhere')->with('condFrom')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('expiryFrom', m::type(\DateTime::class))->once()->andReturnSelf();

        $qb->shouldReceive('expr->lte')->with('m.expiryDate', ':expiryTo')->once()->andReturn('condTo');
        $qb->shouldReceive('andWhere')->with('condTo')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('expiryTo', m::type(\DateTime::class))->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('ta.id', ':trafficArea')->once()->andReturn('condTa');
        $qb->shouldReceive('andWhere')->with('condTa')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('trafficArea', 'B')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')
            ->andReturn('RESULT');

        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);
        $this->em->shouldReceive('getRepository')
            ->with(Licence::class)
            ->andReturn($repo);

        $result = $this->sut->fetchForContinuation(2015, 1, 'B');
        $this->assertEquals('RESULT', $result);
    }

    public function testFetchForContinuationNotSought()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()
                ->shouldReceive('getResult')
                ->once()
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        $this->queryBuilder
            ->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->andReturnSelf();

        $now = new DateTime();

        $this->assertEquals(['RESULTS'], $this->sut->fetchForContinuationNotSought($now));

        $expectedQuery = '[QUERY] AND m.expiryDate < [[' . $now->format(\DateTime::W3C) . ']] '
            . 'AND m.status IN [[["lsts_valid","lsts_curtailed","lsts_suspended"]]] '
            . 'AND (m.goodsOrPsv = [[lcat_gv]] OR (m.goodsOrPsv = [[lcat_psv]] AND m.licenceType = [[ltyp_sr]])) '
            . 'INNER JOIN m.fees f INNER JOIN f.feeType ft AND f.feeStatus = [[lfs_ot]] AND ft.feeType = [[CONT]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
