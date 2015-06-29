<?php

/**
 * Vehicle test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Vehicle as VehicleRepo;

/**
 * Vehicle test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(VehicleRepo::class);
    }

    public function testFetchByVrm()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchLicencesForVrm('ABC123'));

        $expectedQuery = '[QUERY] INNER JOIN m.licenceVehicles lv INNER JOIN lv.licence l'
            . ' AND lv.removalDate IS NULL AND m.vrm = [[ABC123]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
