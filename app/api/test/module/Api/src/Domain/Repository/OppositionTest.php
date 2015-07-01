<?php

/**
 * Opposition Repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Opposition as Repo;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\EntityRepository;

/**
 * Opposition Repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class OppositionTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchUsingCaseId()
    {
        $id = 99;
        $case = 24;
        $mockResult = [0 => 'result'];

        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($id);
        $command->shouldReceive('getCase')
            ->andReturn($case);

        /** @var Expr $expr */
        $expr = m::mock(QueryBuilder::class);
        $expr->shouldReceive('eq')
            ->with(m::type('string'), ':byCase')
            ->andReturnSelf();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('expr')
            ->andReturn($expr);

        $qb->shouldReceive('setParameter')
            ->with('byCase', $case)
            ->andReturnSelf();

        $qb->shouldReceive('andWhere')
            ->with($expr)
            ->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefData')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('case')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('opposer', 'o')
            ->andReturnSelf()
            ->shouldReceive('withPersonContactDetails')
            ->once()
            ->with('o.contactDetails', 'c')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('createdBy')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('lastModifiedBy')
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with($id);

        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($mockResult);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Opposition::class)
            ->andReturn($repo);

        $result = $this->sut->fetchUsingCaseId($command, Query::HYDRATE_OBJECT);

        $this->assertEquals($result, $mockResult[0]);
    }

    public function testApplyListJoins()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockQb = m::mock(QueryBuilder::class);

        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);
        $mockQb->shouldReceive('with')->with('application')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('case', 'ca')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('opposer', 'o')->once()->andReturnSelf();
        $mockQb->shouldReceive('withPersonContactDetails')->with('o.contactDetails')->once()
            ->andReturnSelf();

        $sut->applyListJoins($mockQb);
    }

    public function testApplyFiltersCase()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $qb = m::mock(QueryBuilder::class);
        $query = m::mock(QueryInterface::class);

        $query->shouldReceive('getCase')->with()->andReturn(746);
        $query->shouldReceive('getLicence')->with()->andReturn(null);

        $qb->shouldReceive('expr->eq')->with('m.case', ':byCase')->once()->andReturn('EXPR');
        $qb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('byCase', 746)->once()->andReturnSelf();

        $sut->applyListFilters($qb, $query);
    }

    public function testApplyFiltersLicence()
    {
        // mock SUT to allow testing the protected method
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $qb = m::mock(QueryBuilder::class);
        $query = m::mock(QueryInterface::class);

        $query->shouldReceive('getCase')->with()->andReturn(null);
        $query->shouldReceive('getLicence')->with()->andReturn(43);

        $qb->shouldReceive('expr->eq')->with('m.licence', ':licence')->once()->andReturn('EXPR');
        $qb->shouldReceive('andWhere')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licence', 43)->once()->andReturnSelf();

        $sut->applyListFilters($qb, $query);
    }

    public function testFetchByApplicationId()
    {
        $applicationId = 69;

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->once()
            ->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)
            ->andReturnSelf()
            ->shouldReceive('with')
            ->with('case', 'c')
            ->andReturnSelf()
            ->shouldReceive('order')
            ->with('createdOn', 'DESC')
            ->andReturnSelf();

        $mockQb
            ->shouldReceive('expr->eq')
            ->with('c.application', ':application')
            ->andReturnSelf();
        $mockQb
            ->shouldReceive('andWhere')
            ->andReturnSelf();
        $mockQb
            ->shouldReceive('setParameter')
            ->with('application', $applicationId)
            ->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('result');

        $this->assertSame(
            'result',
            $this->sut->fetchByApplicationId($applicationId)
        );
    }
}
