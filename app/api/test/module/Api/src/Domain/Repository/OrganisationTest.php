<?php

/**
 * Organisation test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Transfer\Query\Organisation\CpidOrganisation;
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
        $this->setUpSut(Repo::class, true);
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

    public function testGetByCompanyOrLlpNoNotFound()
    {
        $companyNumber = '01234567';

        $results = [];

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('expr->eq');
        $qb
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
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

        $this->setExpectedException(NotFoundException::class);

        $this->sut->getByCompanyOrLlpNo($companyNumber);
    }

    public function testFetchByStatusPaginatedWithNullStatus()
    {
        $query = m::mock(CpidOrganisation::class)->makePartial();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->andReturnSelf()
            ->shouldReceive('order')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturnSelf();

        $qb->shouldReceive('where');
        $qb->shouldReceive('expr->isNull');

        $this->sut->shouldReceive('fetchPaginatedList');
        $this->sut->shouldReceive('fetchPaginatedCount');

        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $this->sut->fetchByStatusPaginated($query);
    }

    public function testFetchByStatusPaginatedWithStatus()
    {
        $query = m::mock(CpidOrganisation::class)
            ->makePartial()
            ->shouldReceive('getCpid')
            ->andReturn('op_cpid_central');

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->andReturnSelf()
            ->shouldReceive('order')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturnSelf();

        $this->sut->shouldReceive('getRefdataReference');

        $qb->shouldReceive('where');
        $qb->shouldReceive('expr->eq');

        $qb->shouldReceive('setParameter');

        $this->sut->shouldReceive('fetchPaginatedList');
        $this->sut->shouldReceive('fetchPaginatedCount');

        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $this->sut->fetchByStatusPaginated($query->getMock());
    }

    public function testFetchAllByStatusForCpidExportWithStatus()
    {
        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->andReturnSelf();

        $qb->shouldReceive('where');
        $qb->shouldReceive('expr->eq');

        $qb->shouldReceive('setParameter');

        $qb->shouldReceive('select')
            ->with('o.id', 'o.name', 'r.id AS cpid');

        $qb->shouldReceive('getQuery')
            ->once()
            ->andReturn(m::mock()->shouldReceive('iterate')->getMock());

        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $this->sut->fetchAllByStatusForCpidExport('op_cpid_central');
    }

    public function testFetchAllByStatusForCpidExportWithNullStatus()
    {
        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->andReturnSelf()
            ->shouldReceive('with')
            ->andReturnSelf();

        $qb->shouldReceive('where');
        $qb->shouldReceive('expr->isNull');

        $qb->shouldReceive('select')
            ->with('o.id', 'o.name', 'r.id AS cpid');

        $qb->shouldReceive('getQuery')
            ->once()
            ->andReturn(m::mock()->shouldReceive('iterate')->getMock());

        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->andReturn($qb);

        $this->em->shouldReceive('getRepository')
            ->with(Organisation::class)
            ->andReturn($repo);

        $this->sut->fetchAllByStatusForCpidExport(null);
    }
}
