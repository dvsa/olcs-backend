<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
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

    public function testFetchUnderConsiderationApplicationIds()
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
                'select epa.id ' .
                'from irhp_permit_application ipa '.
                'inner join ecmt_permit_application epa on ipa.ecmt_permit_application_id = epa.id '.
                'inner join irhp_permit_window ipw on ipa.irhp_permit_window_id = ipw.id '.
                'where ipw.irhp_permit_stock_id = :stockId '.
                'and epa.status = :status',
                [
                    'stockId' => $stockId,
                    'status' => EcmtPermitApplicationEntity::STATUS_UNDER_CONSIDERATION
                ]
            )
            ->once()
            ->andReturn($statement);

        $this->em->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->assertEquals(
            [14, 15, 16],
            $this->sut->fetchUnderConsiderationApplicationIds($stockId)
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
}
