<?php

/**
 * Application test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Doctrine\ORM\OptimisticLockException;
use Dvsa\Olcs\Api\Domain\Exception\VersionConflictException;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * Application test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(ApplicationRepo::class);
    }

    public function testLockWithoutApplication()
    {
        $this->setExpectedException(RuntimeException::class);

        $this->sut->lock(m::mock(Licence::class), 1);
    }

    public function testLockWithConflict()
    {
        $entity = m::mock(Application::class);

        $this->em->shouldReceive('lock')
            ->with($entity, LockMode::OPTIMISTIC, 1)
            ->andThrow(OptimisticLockException::class);

        $this->setExpectedException(VersionConflictException::class);

        $this->sut->lock($entity, 1);
    }

    public function testLock()
    {
        $entity = m::mock(Application::class);

        $this->em->shouldReceive('lock')
            ->with($entity, LockMode::OPTIMISTIC, 1);

        $this->sut->lock($entity, 1);
    }

    public function testSaveWithoutApplication()
    {
        $this->setExpectedException(RuntimeException::class);

        $this->sut->save(m::mock(Licence::class));
    }

    public function testSave()
    {
        $entity = m::mock(Application::class);

        $this->em->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->shouldReceive('flush')
            ->once();

        $this->sut->save($entity);
    }

    public function testDeleteWithoutApplication()
    {
        $this->setExpectedException(RuntimeException::class);

        $this->sut->delete(m::mock(Licence::class));
    }

    public function testDelete()
    {
        $entity = m::mock(Application::class);

        $this->em->shouldReceive('remove')
            ->once()
            ->with($entity)
            ->shouldReceive('flush')
            ->once();

        $this->sut->delete($entity);
    }

    public function testGetRefdataReference()
    {
        $id = 'foo';

        $this->em->shouldReceive('getReference')
            ->with(RefData::class, $id)
            ->andReturn('blah');

        $this->assertEquals('blah', $this->sut->getRefdataReference($id));
    }

    public function testGetCategoryReference()
    {
        $id = 'foo';

        $this->em->shouldReceive('getReference')
            ->with(Category::class, $id)
            ->andReturn('blah');

        $this->assertEquals('blah', $this->sut->getCategoryReference($id));
    }

    public function testGetSubCategoryReference()
    {
        $id = 'foo';

        $this->em->shouldReceive('getReference')
            ->with(SubCategory::class, $id)
            ->andReturn('blah');

        $this->assertEquals('blah', $this->sut->getSubCategoryReference($id));
    }

    public function testGetReference()
    {
        $id = 'foo';

        $this->em->shouldReceive('getReference')
            ->with(RefData::class, $id)
            ->andReturn('blah');

        $this->assertEquals('blah', $this->sut->getReference(RefData::class, $id));
    }

    public function testFetchUsingId()
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
            ->shouldReceive('with')
            ->once()
            ->with('licence')
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('a')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchUsingId($command, Query::HYDRATE_OBJECT, 1);
    }

    public function testFetchUsingIdWithResults()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        $result = m::mock(Application::class);
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
            ->shouldReceive('with')
            ->once()
            ->with('licence')
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('a')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchUsingId($command, Query::HYDRATE_OBJECT, 1);
    }

    public function testFetchWithLicenceAndOc()
    {
        $applicationId = 1;

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('getQuery->getSingleResult')
            ->andReturn('RESULT');

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('licence', 'l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l.operatingCentres', 'l_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_oc.operatingCentre', 'l_oc_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('l_oc_oc.address', 'l_oc_oc_a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('operatingCentres', 'a_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('a_oc.operatingCentre', 'a_oc_oc')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('with')
            ->with('a_oc_oc.address', 'a_oc_oc_a')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($applicationId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo);

        $result = $this->sut->fetchWithLicenceAndOc($applicationId);
        $this->assertEquals('RESULT', $result);
    }

    public function testFetchActiveForOrganisation()
    {
        $organisationId = 123;

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('expr->eq')
            ->with('l.organisation', ':organisationId');

        $qb->shouldReceive('expr->in')
            ->with('a.status', ['apsts_consideration', 'apsts_granted']);

        $qb
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('organisationId', $organisationId)
            ->shouldReceive('getQuery->execute')
            ->andReturn('RESULT');

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('licence', 'l')
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo);

        $result = $this->sut->fetchActiveForOrganisation($organisationId);

        $this->assertEquals('RESULT', $result);
    }

    public function testFetchWithLicence()
    {
        $applicationId = 1;

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('getQuery->getResult')
            ->andReturn(['RESULT']);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('licence', 'l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($applicationId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo);

        $result = $this->sut->fetchWithLicence($applicationId);
        $this->assertEquals('RESULT', $result);
    }

    public function testFetchWithLicenceAndOrg()
    {
        $applicationId = 1;

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('getQuery->getResult')
            ->andReturn(['RESULT']);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('licence', 'l')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('l.organisation', 'l_org')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($applicationId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo);

        $result = $this->sut->fetchWithLicenceAndOrg($applicationId);
        $this->assertEquals('RESULT', $result);
    }

    public function testFetchWithLicenceNotFound()
    {
        $applicationId = 1;

        $this->setExpectedException(NotFoundException::class);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('getQuery->getResult')
            ->andReturn([]);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('licence', 'l')
            ->andReturnSelf()
            ->once()
            ->shouldReceive('byId')
            ->with($applicationId)
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo);

        $result = $this->sut->fetchWithLicence($applicationId);
        $this->assertEquals('RESULT', $result);
    }

    public function testApplyListJoins()
    {
        $this->setUpSut(ApplicationRepo::class, true);

        $this->sut->shouldReceive('getQueryBuilder')->with()->once()->andReturnSelf();
        $this->sut->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();

        $mockQb = m::mock(QueryBuilder::class);
        $this->sut->applyListJoins($mockQb);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(ApplicationRepo::class, true);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('l.organisation', ':organisation')->once()->andReturn('EXPR1');
        $mockQb->shouldReceive('setParameter')->with('organisation', 723)->once()->andReturn();
        $mockQb->shouldReceive('andWhere')->with('EXPR1')->once()->andReturnSelf();

        $mockQuery = m::mock(QueryInterface::class);
        $mockQuery->shouldReceive('getOrganisation')->with()->andReturn(723);

        $this->sut->applyListFilters($mockQb, $mockQuery);
    }

    public function testFetchWithTmLicences()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('a')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.tmLicences', 'ltml')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(1)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getSingleResult')->once()->andReturn(['RESULT']);
        $this->assertEquals(['RESULT'], $this->sut->fetchWithTmLicences(1));
    }
}
