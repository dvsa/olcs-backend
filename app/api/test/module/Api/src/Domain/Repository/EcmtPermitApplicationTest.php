<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Mockery as m;

/**
 * ECMT Permit Application test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtPermitApplicationTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(EcmtPermitApplication::class);
    }

    public function testFetchApplicationIdsAwaitingScoring()
    {
        $stockId = 14;

        $statement = m::mock(Statement::class);
        $statement->shouldReceive('fetchAll')
            ->once()
            ->andReturn(
                [
                    [ 'id' => 14 ],
                    [ 'id' => 15 ],
                    [ 'id' => 16 ],
                ]
            );

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')
            ->with(
                'select e.id from ecmt_permit_application e ' .
                'inner join licence as l on e.licence_id = l.id ' .
                'where e.id in (' .
                '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
                '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
                '    )' .
                ') ' .
                'and e.status = :status ' .
                'and (l.licence_type = :licenceType1 or l.licence_type = :licenceType2)',
                [
                    'stockId' => $stockId,
                    'status' => EcmtPermitApplicationEntity::STATUS_UNDER_CONSIDERATION,
                    'licenceType1' => LicenceEntity::LICENCE_TYPE_RESTRICTED,
                    'licenceType2' => LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL
                ]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->assertEquals(
            [14, 15, 16],
            $this->sut->fetchApplicationIdsAwaitingScoring($stockId)
        );
    }

    public function testFetchInScopeApplicationIds()
    {
        $stockId = 14;

        $statement = m::mock(Statement::class);
        $statement->shouldReceive('fetchAll')
            ->once()
            ->andReturn(
                [
                    [ 'id' => 14 ],
                    [ 'id' => 15 ],
                    [ 'id' => 16 ],
                ]
            );

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')
            ->with(
                'select e.id from ecmt_permit_application e ' .
                'where e.id in (' .
                '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
                '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
                '    )' .
                ') ' .
                'and e.in_scope = 1 ',
                ['stockId' => $stockId]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->assertEquals(
            [14, 15, 16],
            $this->sut->fetchInScopeApplicationIds($stockId)
        );
    }

    public function testFetchByWindowId()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByWindowId('ID', ['S1', 'S2']));

        $expectedQuery = 'BLAH '
            . 'INNER JOIN epa.irhpPermitApplications ipa '
            . 'INNER JOIN ipa.irhpPermitWindow ipw '
            . 'AND ipw.id = [[ID]] '
            . 'AND epa.status IN [[["S1","S2"]]]';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testClearScope()
    {
        $stockId = 7;

        $statement = m::mock(Statement::class);
        $statement->shouldReceive('execute')
            ->once();

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')
            ->with(
                'update ecmt_permit_application e ' .
                'set e.in_scope = 0 ' .
                'where e.id in (' .
                '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
                '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
                '    )' .
                ')',
                ['stockId' => $stockId]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->sut->clearScope($stockId);
    }

    public function testApplyScope()
    {
        $stockId = 7;

        $statement = m::mock(Statement::class);
        $statement->shouldReceive('execute')
            ->once();

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')
            ->with(
                'update ecmt_permit_application as e ' .
                'inner join licence as l on e.licence_id = l.id ' .
                'set e.in_scope = 1 ' .
                'where e.id in (' .
                '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
                '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
                '    )' .
                ') ' .
                'and e.status = :status ' .
                'and (l.licence_type = :licenceType1 or l.licence_type = :licenceType2)',
                [
                    'stockId' => $stockId,
                    'status' => EcmtPermitApplicationEntity::STATUS_UNDER_CONSIDERATION,
                    'licenceType1' => LicenceEntity::LICENCE_TYPE_RESTRICTED,
                    'licenceType2' => LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL
                ]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->sut->applyScope($stockId);
    }

    public function testFetchApplicationIdToCountryIdAssociations()
    {
        $stockId = 14;

        $associations = [
            102 => 'AT',
            102 => 'RU',
            103 => 'GR'
        ];

        $statement = m::mock(Statement::class);
        $statement->shouldReceive('fetchAll')
            ->once()
            ->andReturn($associations);

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')
            ->with(
                'select e.id as ecmtApplicationId, eacl.country_id as countryId ' .
                'from ecmt_application_country_link eacl ' .
                'inner join ecmt_permit_application as e on e.id = eacl.ecmt_application_id ' .
                'where e.id in (' .
                '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
                '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
                '    )' .
                ') ' .
                'and e.in_scope = 1 ',
                ['stockId' => $stockId]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->assertEquals(
            $associations,
            $this->sut->fetchApplicationIdToCountryIdAssociations($stockId)
        );
    }
}
