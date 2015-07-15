<?php

/**
 * Organisation test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as Repo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * Organisation test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationTest extends RepositoryTestCase
{
    /**
     * @var Repo
     */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchBusinessDetailsByIdNotFound()
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
            ->shouldReceive('withContactDetails')
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('o')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchBusinessDetailsUsingId($command);
    }

    public function testFetchBusinessDetailsById()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn(
                [
                    [
                        'foo' => 'bar'
                    ]
                ]
            );

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
            ->shouldReceive('withContactDetails')
            ->once()
            ->andReturnSelf();

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('o')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $result = $this->sut->fetchBusinessDetailsUsingId($command);

        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testFetchIrfoDetailsById()
    {
        $command = m::mock(QueryInterface::class);
        $command->shouldReceive('getId')
            ->andReturn(111);

         /** @var Expr $expr */
        $expr = m::mock(QueryBuilder::class);
        $expr->shouldReceive('isNull')
            ->once()
            ->with('tn.licence')
            ->andReturnSelf();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);
        $qb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn(
                [
                    [
                        'foo' => 'bar'
                    ]
                ]
            );
        $qb->shouldReceive('expr')
            ->andReturn($expr);
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
            ->with('irfoNationality')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('irfoPartners')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->once()
            ->with('tradingNames', 'tn')
            ->andReturnSelf()
            ->shouldReceive('withContactDetails')
            ->once()
            ->with('irfoContactDetails')
            ->andReturnSelf()
            ->shouldReceive('byId')
            ->once()
            ->with(111);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('o')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $result = $this->sut->fetchIrfoDetailsUsingId($command);

        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testGetByCompanyOrLlpNo()
    {
        $companyNumber = '01234567';

        $result = m::mock(Organisation::class);
        $results = [$result];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $where = m::mock();
        $qb->shouldReceive('expr->eq')
            ->with('o.companyOrLlpNo', ':companyNumber')
            ->andReturn($where);
        $qb
            ->shouldReceive('andWhere')
            ->with($where)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('companyNumber', $companyNumber)
            ->once()
            ->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')
            ->andReturn($results);

        /** @var EntityRepository $repo */
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $this->sut->getByCompanyOrLlpNo($companyNumber);
    }
}
