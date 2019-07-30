<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Mockery as m;

/**
 * Irhp Application test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpApplicationTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpApplication::class);
    }

    public function testFetchLicenceByOrganisation()
    {
        $expectedResult = [1, 7, 706];
        $organisation = 1;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('l.id')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(LicenceEntity::class, 'l')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('l.organisation = ' . $organisation)
            ->andReturnSelf()
            ->shouldReceive('getQuery->execute')
            ->once()
            ->andReturn(
                [
                    [
                        'id' => 1,
                    ],
                    [
                        'id' => 7,
                    ],
                    [
                        'id' => 706,
                    ]
                ]
            );

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchLicenceByOrganisation($organisation)
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
            . 'INNER JOIN ia.irhpPermitApplications ipa '
            . 'INNER JOIN ipa.irhpPermitWindow ipw '
            . 'AND ipw.id = [[ID]] '
            . 'AND ia.status IN [[["S1","S2"]]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
