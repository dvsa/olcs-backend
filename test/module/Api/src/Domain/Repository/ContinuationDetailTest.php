<?php

/**
 * ContinuationDetailTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as Repo;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as Entity;

/**
 * ContinuationDetailTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationDetailTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchForLicence()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf()
            ->shouldReceive('with')->with('continuation', 'c')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchForLicence(95));

        $dateTime = new \Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime();
        $year = $dateTime->format('Y');
        $futureYear = $year + 4;
        $month = $dateTime->format('n');
        $pastYear = $year - 4;

        $expectedQuery = <<<EOT
BLAH AND m.licence = [[95]]
    AND l.status IN [[["lsts_valid","lsts_curtailed","lsts_suspended"]]]
    AND (c.month >= [[$month]] AND c.year = [[$year]])
        OR (c.year > [[$year]] AND c.year < [[$futureYear]])
        OR (c.month <= [[$month]] AND c.year = [[$futureYear]])
        OR (c.month <= [[$month]] AND c.year = [[$year]])
        OR (c.year > [[$pastYear]] AND c.year < [[$year]])
        OR (c.month >= [[$month]] AND c.year = [[$pastYear]])
    AND m.status IN ([[["con_det_sts_printed","con_det_sts_acceptable","con_det_sts_unacceptable"]]])
        OR (m.status = 'con_det_sts_complete' AND m.received = 'N')
EOT;
        // Expected query has be formatted to make it readable, need to make it non formatted for assertion
        // remove new lines
        $expectedQuery = str_replace("\n", ' ', $expectedQuery);
        // remove indentation
        $expectedQuery = str_replace("  ", '', $expectedQuery);

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchOngoingForLicence()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('continuation', 'c')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getSingleResult')
                ->andReturn('RESULT')
                ->getMock()
        );
        $this->assertEquals('RESULT', $this->sut->fetchOngoingForLicence(95));

        $expectedQuery = 'BLAH AND m.licence = [[95]] AND m.status = [[con_det_sts_acceptable]]';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchChecklistReminders()
    {
        $mockQb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->twice()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('continuation', 'c')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.status', 'ls')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.licenceType', 'lt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.goodsOrPsv', 'lgp')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.organisation', 'lo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.fees', 'lf')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('lf.feeType', 'lfft')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('lfft.feeType', 'lfftft')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('lf.feeStatus', 'lffs')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->in')->with('l.status', ':licenceStatuses')->once()->andReturn('conditionLic');
        $mockQb->shouldReceive('andWhere')->with('conditionLic')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with(
                'licenceStatuses',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED
                ]
            )
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.received', 0)->once()->andReturn('conditionReceived');
        $mockQb->shouldReceive('andWhere')->with('conditionReceived')->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('filterByIds')->with([1])->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')
            ->with('c.month', ':month')
            ->once()
            ->andReturn('conditionMonth');
        $mockQb->shouldReceive('andWhere')->with('conditionMonth')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('month', 1)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')
            ->with('c.year', ':year')
            ->once()
            ->andReturn('conditionYear');
        $mockQb->shouldReceive('andWhere')->with('conditionYear')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('year', 2016)
            ->once()
            ->andReturnSelf();

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('m')
            ->once()
            ->andReturn($mockQb);

        $result = [
            [
                'licence' => [
                    'fees' => [
                        [
                            'feeStatus' => [
                                'id' => FeeEntity::STATUS_OUTSTANDING
                            ],
                            'feeType' => [
                                'feeType' => [
                                    'id' => FeeTypeEntity::FEE_TYPE_APP
                                ]
                            ]
                        ],
                        [
                            'feeStatus' => [
                                'id' => FeeEntity::STATUS_OUTSTANDING
                            ],
                            'feeType' => [
                                'feeType' => [
                                    'id' => FeeTypeEntity::FEE_TYPE_CONT
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'licence' => [
                    'fees' => []
                ]
            ]
        ];
        $expected = [
            [
                'licence' => [
                    'fees' => []
                ]
            ]
        ];
        $mockQb->shouldReceive('getQuery->getResult')
            ->with(\Doctrine\ORM\Query::HYDRATE_ARRAY)
            ->once()
            ->andReturn($result);

        $this->assertEquals($expected, $this->sut->fetchChecklistReminders(1, 2016, [1]));
    }

    /**
     * @dataProvider statusProvider
     */
    public function testFetchDetails($method, $allowEmail)
    {
        $mockQb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('continuation', 'c')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('status', 's')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.status', 'ls')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.organisation', 'lo')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.licenceType', 'lt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.goodsOrPsv', 'lg')->once()->andReturnSelf();

        $mockQb->shouldReceive('orderBy')->with('l.licNo', 'ASC')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('c.id', ':continuationId')->once()->andReturn('conditionContId');
        $mockQb->shouldReceive('andWhere')->with('conditionContId')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('continuationId', 1)->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->in')->with('l.status', ':licenceStatuses')->once()->andReturn('conditionLicSt');
        $mockQb->shouldReceive('andWhere')->with('conditionLicSt')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licenceStatuses', ['st'])->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('l.licNo', ':licNo')->once()->andReturn('conditionLicNo');
        $mockQb->shouldReceive('andWhere')->with('conditionLicNo')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licNo', 'ln')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('lo.allowEmail', $allowEmail)->once()->andReturn('conditionMethod');
        $mockQb->shouldReceive('andWhere')->with('conditionMethod')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.status', ':status')->once()->andReturn('conditionStatus');
        $mockQb->shouldReceive('andWhere')->with('conditionStatus')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('status', 'st')->once()->andReturnSelf();

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('m')
            ->once()
            ->andReturn($mockQb);

        $mockQb->shouldReceive('getQuery->getResult')
            ->with(\Doctrine\ORM\Query::HYDRATE_OBJECT)
            ->once()
            ->andReturn(['result']);

        $this->assertEquals(
            $this->sut->fetchDetails(1, ['st'], 'ln', $method, 'st'),
            ['result']
        );
    }

    public function statusProvider()
    {
        return [
            [Entity::METHOD_EMAIL, 1],
            [Entity::METHOD_POST, 0]
        ];
    }

    public function testFetchWithLicence()
    {
        $mockQb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('status', 's')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.licenceType', 'lt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.goodsOrPsv', 'lg')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(1)->once()->andReturnSelf();

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('m')
            ->once()
            ->andReturn($mockQb);

        $mockQb->shouldReceive('getQuery->getSingleResult')
            ->once()
            ->andReturn(['result']);

        $this->assertEquals(
            $this->sut->fetchWithLicence(1),
            ['result']
        );
    }

    public function testFetchLicenceIdsForContinuationAndLicences()
    {
        $mockQb = m::mock(QueryBuilder::class);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->in')->with('m.licence', ':licences')->once()->andReturn('licences');
        $mockQb->shouldReceive('andWhere')->with('licences')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('licences', [222, 333])->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('m.continuation', ':continuation')->once()->andReturn('continuation');
        $mockQb->shouldReceive('andWhere')->with('continuation')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('continuation', 111)->once()->andReturnSelf();

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('m')
            ->once()
            ->andReturn($mockQb);

        $mockQb->shouldReceive('getQuery->getResult')
            ->with(\Doctrine\ORM\Query::HYDRATE_ARRAY)
            ->once()
            ->andReturn([['licence' => ['id' => 123]]]);

        $this->assertEquals(
            $this->sut->fetchLicenceIdsForContinuationAndLicences(111, [222, 333]),
            [123]
        );
    }

    public function testCreateContinuationDetails()
    {
        $query = m::mock();
        $query->shouldReceive('executeInsert')->once()->with([1], false, 'status', 2);

        $this->dbQueryService->shouldReceive('get')->with('Continuations\CreateContinuationDetails')->andReturn($query);

        $this->sut->createContinuationDetails([1], false, 'status', 2);
    }
}
