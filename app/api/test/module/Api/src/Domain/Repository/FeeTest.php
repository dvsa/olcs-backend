<?php

/**
 * Fee test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\Fee\FeeList as FeeListQry;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;

/**
 * Fee test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class FeeTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(FeeRepo::class, true);
    }

    private function setupFetchInterimFeesByApplicationId($mockQb, $applicationId)
    {
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('f')->once()->andReturn($mockQb);
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('order')->with('invoicedDate', 'ASC')->once()->andReturnSelf();

        $mockQb->shouldReceive('join')->with('f.feeType', 'ft')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('ft.feeType', ':feeTypeFeeType')->once()->andReturn('foo');
        $mockQb->shouldReceive('andWhere')->with('foo')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('f.application', ':applicationId')->once()->andReturn('bar');
        $mockQb->shouldReceive('andWhere')->with('bar')->once()->andReturnSelf();

        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_GRANTINT
        )->once()->andReturn('refdata');
        $mockQb->shouldReceive('setParameter')->with('feeTypeFeeType', 'refdata')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('applicationId', $applicationId)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('result');
    }

    public function testFetchInterimFeesByApplicationId()
    {
        $mockQb = m::mock();

        $this->setupFetchInterimFeesByApplicationId($mockQb, 33);

        $this->assertSame('result', $this->sut->fetchInterimFeesByApplicationId(33));
    }

    public function testFetchInterimFeesByApplicationIdOutstanding()
    {
        $mockQb = m::mock();

        $this->setupFetchInterimFeesByApplicationId($mockQb, 12);

        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\Fee::STATUS_OUTSTANDING
        )->once()->andReturn('ot');
        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\Fee::STATUS_WAIVE_RECOMMENDED
        )->once()->andReturn('wr');

        $mockQb->shouldReceive('expr->in')->with('f.feeStatus', ':feeStatus')->once()->andReturn('expr-in');
        $mockQb->shouldReceive('andWhere')->with('expr-in')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('feeStatus', ['ot', 'wr'])->once();

        $this->assertSame('result', $this->sut->fetchInterimFeesByApplicationId(12, true));
    }

    public function testFetchOutstandingFeesByOrganisationId()
    {
        $organisationId = 123;

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('f')
            ->once()
            ->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            //->withAnyArgs()
            ->andReturnSelf()
            ->shouldReceive('order')
            ->with('invoicedDate', 'ASC')
            ->once()
            ->andReturnSelf();

        $this->mockWhereOutstandingFee($mockQb);

        $this->mockWhereCurrentLicenceOrApplicationFee($mockQb, $organisationId);

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('result');

        $this->assertSame(
            'result',
            $this->sut->fetchOutstandingFeesByOrganisationId($organisationId)
        );
    }

    public function testFetchOutstandingFeesByIds()
    {
        $ids = [1, 2, 3];

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('f')
            ->once()
            ->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->andReturnSelf()
            ->shouldReceive('order')
            ->with('invoicedDate', 'ASC')
            ->once()
            ->andReturnSelf();

        $this->mockWhereOutstandingFee($mockQb);

        $mockQb
            ->shouldReceive('expr->eq');
        $mockQb
            ->shouldReceive('expr->in');
        $mockQb
            ->shouldReceive('andWhere')
            ->andReturnSelf();
        $mockQb
            ->shouldReceive('setParameter')
            ->with('feeIds', $ids)
            ->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('result');

        $this->assertSame(
            'result',
            $this->sut->fetchOutstandingFeesByIds($ids)
        );
    }

    /**
     * @param string $status
     * @dataProvider statusProvider
     */
    public function testFetchList($status)
    {
        // in practice this query would never return results, but it covers all
        // possible conditions
        $query = FeeListQry::create(
            [
                'application' => 11,
                'licence' => 12,
                'task' => 13,
                'busReg' => 14,
                'irfoGvPermit' => 15,
                'page' => 1,
                'limit' => 10,
                'sort' => 'id',
                'order' => 'ASC',
                'isMiscellaneous' => true,
                'ids' => [1,2,3],
                'status' => $status,
            ]
        );

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('f')
            ->once()
            ->andReturn($mockQb);

         $this->queryBuilder->shouldReceive('modifyQuery')
            ->with($mockQb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->once()
            ->with(1, 10)
            ->andReturnSelf()
            ->shouldReceive('order')
            ->once()
            ->with('id', 'ASC')
            ->andReturnSelf()
            ->shouldReceive('withCreatedBy')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('filterByLicence')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('filterByApplication')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('filterByIds')
            ->once()
            ->andReturnSelf();

        $busRegQb = m::mock(QueryBuilder::class);
        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('br')
            ->once()
            ->andReturn($busRegQb);
        $busRegQb
            ->shouldReceive('select')
            ->andReturnSelf()
            ->shouldReceive('join')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->once()
            ->with('id', 14)
            ->andReturnSelf();
        $busRegIds = [14, 15, 16];
        $busRegQb
            ->shouldReceive('getQuery->getArrayResult')
            ->once()
            ->andReturn($busRegIds);

        // we *could* assert all the conditions here, but just stub the methods for now
        $mockQb
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->in');

        // mock pagination
        $mockQuery = m::mock();
        $mockQb->shouldReceive('getQuery')->andReturn($mockQuery);
        $mockQuery->shouldReceive('setHydrationMode');
        $paginator = m::mock();
        $this->sut->shouldReceive('getPaginator')->andReturn($paginator);
        $paginator->shouldReceive('getIterator')->andReturn('result');

        $this->assertSame(
            'result',
            $this->sut->fetchList($query)
        );
    }

    public function statusProvider()
    {
        return [
            ['all'],
            ['current'],
            ['historical'],
        ];
    }

    public function testFetchLatestFeeByTypeStatusesAndApplicationId()
    {
        $feeType = 'APP';
        $feeStatuses = ['lfs_ot', 'lfs_w'];
        $applicationId = 69;

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('f')
            ->once()
            ->andReturn($mockQb);

        $this->queryBuilder
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('with')
            ->andReturnSelf()
            ->shouldReceive('order')
            ->with('invoicedDate', 'DESC')
            ->once()
            ->andReturnSelf();

        $mockQb
            ->shouldReceive('expr->in');
        $mockQb
            ->shouldReceive('expr->eq');
        $mockQb
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('application', $applicationId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('feeType', $feeType)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('feeStatuses', $feeStatuses)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->andReturnSelf();

        $fee1 = m::mock();
        $results = [$fee1];
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn($results);

        $this->assertSame(
            $fee1,
            $this->sut->fetchLatestFeeByTypeStatusesAndApplicationId($feeType, $feeStatuses, $applicationId)
        );
    }

    public function testFetchOutstandingFeesByApplicationId()
    {
        $applicationId = 69;

        /** @var QueryBuilder $qb */
        $mockQb = m::mock(QueryBuilder::class);

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('f')
            ->once()
            ->andReturn($mockQb);

        $this->mockWhereOutstandingFee($mockQb);

        $mockQb
            ->shouldReceive('expr->eq')
            ->with('f.application', ':application')
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
            $this->sut->fetchOutstandingFeesByApplicationId($applicationId)
        );
    }

    private function mockWhereOutstandingFee($mockQb)
    {
        $where = m::mock();
        $mockQb
            ->shouldReceive('expr->in')
            ->with('f.feeStatus', ':feeStatus')
            ->once()
            ->andReturn($where);

        $mockQb
            ->shouldReceive('setParameter')
            ->with('feeStatus', m::type('array')); // refdata ['lfs_ot', 'lfs_wr']
        $mockQb
            ->shouldReceive('andWhere')
            ->with($where);

        $this->em
            ->shouldReceive('getReference');
    }

    private function mockWhereCurrentLicenceOrApplicationFee($mockQb, $organisationId)
    {
        $mockQb
            ->shouldReceive('leftJoin')
            ->with('f.application', 'a')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('leftJoin')
            ->with('f.licence', 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('leftJoin')
            ->with('a.licence', 'al')
            ->once()
            ->andReturnSelf();

        $mockQb
            ->shouldReceive('expr->eq');
        $mockQb
            ->shouldReceive('expr->in');
        $mockQb
            ->shouldReceive('expr->orX');
        $mockQb
            ->shouldReceive('expr->isNotNull');
        $mockQb
            ->shouldReceive('andWhere')
            ->andReturnSelf();

        $mockQb
            ->shouldReceive('setParameter')
            ->with('organisationId', $organisationId)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('appStatus', m::type('array')) // refdata ['apsts_consideration', 'apsts_granted']
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('licStatus', m::type('array')); // refdata ['lsts_valid', 'lsts_curtailed', 'lsts_suspended']

        $this->em
            ->shouldReceive('getReference');
    }

    public function testfetchOutstandingGrantFeesByApplicationId()
    {
        $mockQb = $this->createMockQb('{QUERY}');

        $this->mockCreateQueryBuilder($mockQb);

        $this->em->shouldReceive('getReference')
            ->andReturnUsing(
                function ($refData, $input) {
                    return $input;
                }
            );

        $mockQb->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn('Foo');

        $this->assertEquals('Foo', $this->sut->fetchOutstandingGrantFeesByApplicationId(111));

        $this->assertEquals(
            '{QUERY}'
            // whereOutstandingFee
            . ' AND f.feeStatus IN [[["lfs_ot","lfs_wr"]]]'
            . ' INNER JOIN f.feeType ft AND f.application = [[111]] AND ft.feeType = [[GRANT]]',
            $this->query
        );
    }

    public function testFetchOutstandingContinuationFeesByLicenceId()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\Fee::STATUS_OUTSTANDING
        )->once()->andReturn('ot');
        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\Fee::STATUS_WAIVE_RECOMMENDED
        )->once()->andReturn('wr');

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchOutstandingContinuationFeesByLicenceId(716));

        $expectedQuery = 'BLAH INNER JOIN f.feeType ft AND f.licence = [[716]] AND '
            . 'ft.feeType = [[CONT]] AND f.feeStatus IN [[["ot","wr"]]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
