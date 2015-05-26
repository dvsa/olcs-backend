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

    public function testBeginTransaction()
    {
        $this->em->shouldReceive('beginTransaction')
            ->once();

        $this->sut->beginTransaction();
    }

    public function testCommit()
    {
        $this->em->shouldReceive('commit')
            ->once();

        $this->sut->commit();
    }

    public function testRollback()
    {
        $this->em->shouldReceive('rollback')
            ->once();

        $this->sut->rollback();
    }

    public function testGetRefdataReference()
    {
        $id = 'foo';

        $this->em->shouldReceive('getReference')
            ->with(RefData::class, $id)
            ->andReturn('blah');

        $this->assertEquals('blah', $this->sut->getRefdataReference($id));
    }

    public function testGetCategpryReference()
    {
        $id = 'foo';

        $this->em->shouldReceive('getReference')
            ->with(Category::class, $id)
            ->andReturn('blah');

        $this->assertEquals('blah', $this->sut->getCategoryReference($id));
    }

    public function testGetSubCategpryReference()
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
            ->shouldReceive('byId')
            ->once()
            ->with(111);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
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
            ->shouldReceive('byId')
            ->once()
            ->with(111);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Application::class)
            ->andReturn($repo)
            ->shouldReceive('lock')
            ->with($result, LockMode::OPTIMISTIC, 1);

        $this->sut->fetchUsingId($command, Query::HYDRATE_OBJECT, 1);
    }
}
