<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewList;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as Repo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as Entity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * BusRegSearchViewTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class BusRegSearchViewTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByRegNo()
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.regNo', ':regNo')->once()->andReturn('EXPR');
        $qb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('regNo', 'REG0001')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULTS']);

        $this->assertSame('RESULTS', $this->sut->fetchByRegNo('REG0001'));
    }

    public function testFetchByRegNoNotFound()
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.regNo', ':regNo')->once()->andReturn('EXPR');
        $qb->shouldReceive('where')->with('EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('regNo', 'REG0001')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn([]);

        $this->setExpectedException(NotFoundException::class);

        $this->sut->fetchByRegNo('REG0001');
    }

    public function testFetchActiveByLicence()
    {
        $activeStatuses = [
            BusReg::STATUS_NEW,
            BusReg::STATUS_VAR,
            BusReg::STATUS_REGISTERED,
            BusReg::STATUS_CANCEL,
        ];

        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.licId', ':licence')->once()->andReturn('L_EXPR');
        $qb->shouldReceive('where')->with('L_EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licence', '611')->once()->andReturnSelf();

        $qb->shouldReceive('expr->in')->with('m.busRegStatus', ':activeStatuses')->once()->andReturn('S_EXPR');
        $qb->shouldReceive('andWhere')->with('S_EXPR')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('activeStatuses', $activeStatuses)->once()->andReturnSelf();

        $qb->shouldReceive('getQuery->getResult')->with()->once()->andReturn(['RESULTS']);

        $this->assertSame(['RESULTS'], $this->sut->fetchActiveByLicence(611));
    }

    public function testFetchDistinctList()
    {
        $qb = m::mock(QueryBuilder::class);
        $repo = m::mock(EntityRepository::class);

        $this->em->shouldReceive('getRepository')->with(Entity::class)->andReturn($repo);

        $repo->shouldReceive('createQueryBuilder')->with('m')->once()->andReturn($qb);

        $context = 'foo';
        $qb->shouldReceive('select')->with('m.' . $context)->once()->andReturnSelf();
        $qb->shouldReceive('distinct')->andReturnSelf();
        $qb->shouldReceive('getQuery->getResult')->with(m::type('integer'))->once()->andReturn(['RESULTS']);

        $mockQuery = m::mock(QueryInterface::class);
        $mockQuery->shouldReceive('getContext')->andReturn($context);

        $this->assertSame(['RESULTS'], $this->sut->fetchDistinctList($mockQuery));
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('licence', 'UB12345')
            ->andReturnSelf()

            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('status', 'foo')
            ->andReturnSelf()

            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('licNo', 'UB1234')
            ->andReturnSelf()

            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('organisationName', 'bar')
            ->andReturnSelf();

        $mockQ = BusRegSearchViewList::create(
            [
                'licId' => '1234',
                'licNo' => 'UB1234',
                'status' => 'foo',
                'organisationName' => 'bar'
            ]
        );

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
