<?php

/**
 * Cases test
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Transfer\Query\Cases\ByLicence;
use Dvsa\Olcs\Transfer\Query\Cases\ByTransportManager;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Cases test
 */
class CasesTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(CasesRepo::class);
    }

    /**
     * @param $qb
     * @return m\MockInterface
     */
    public function getMockRepo($qb)
    {
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        return $repo;
    }

    public function testApplyListFiltersTm()
    {
        $sut = m::mock(CasesRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $transportManager = 3;

        $mockQuery = m::mock(ByTransportManager::class);
        $mockQuery->shouldReceive('getTransportManager')
            ->once()
            ->andReturn($transportManager)
            ->getMock();

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.transportManager', ':byTransportManager')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('byTransportManager', $transportManager)->once()->andReturnSelf();

        $sut->applyListFilters($mockQb, $mockQuery);
    }

    public function testApplyListFiltersLicence()
    {
        $sut = m::mock(CasesRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $licence = 7;

        $mockQuery = m::mock(ByLicence::class);
        $mockQuery->shouldReceive('getLicence')
            ->once()
            ->andReturn($licence)
            ->getMock();

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.licence', ':byLicence')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('byLicence', $licence)->once()->andReturnSelf();

        $sut->applyListFilters($mockQb, $mockQuery);
    }

    public function testFetchWithLicenceUsingId()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')->andReturn(24);

        $result = m::mock(CasesEntity::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')->with(Query::HYDRATE_OBJECT)->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->once()->with('licence', 'l')->andReturnSelf()
            ->shouldReceive('with')->once()->with('l.operatingCentres', 'loc')->andReturnSelf()
            ->shouldReceive('with')->once()->with('loc.operatingCentre', 'oc')->andReturnSelf()
            ->shouldReceive('with')->once()->with('oc.address')->andReturnSelf()
            ->shouldReceive('byId')->once()->with(24);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(CasesEntity::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchWithLicenceUsingId($command, Query::HYDRATE_OBJECT, 1);
    }

    public function testFetchWithLicence()
    {
        $caseId = 1;

        $result = m::mock(CasesEntity::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('licence', 'l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('application', 'a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('transportManager', 'tm')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($caseId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(CasesEntity::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchExtended($caseId);
    }

    public function testFetchWithLicenceNotFound()
    {
        $caseId = 1;

        $results = null;

        $this->setExpectedException(Exception\NotFoundException::class);
        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('licence', 'l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('application', 'a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('transportManager', 'tm')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($caseId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(CasesEntity::class)
            ->andReturn($repo);

        $this->sut->fetchExtended($caseId);
    }

    public function testBuildDefaultListQuery()
    {
        $sut = m::mock(CasesRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockQi = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);

        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);

        $mockQb->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $mockQb->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('caseType', 'ct')->once()->andReturnSelf();
        $mockQb->shouldReceive('addSelect')->with('CONCAT(ct.description, m.id) as HIDDEN caseType')->once()
            ->andReturnSelf();

        $sut->buildDefaultListQuery($mockQb, $mockQi);
    }
}
