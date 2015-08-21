<?php

/**
 * Psv Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\LicenceVehicle;


use Dvsa\Olcs\Api\Domain\QueryHandler\LicenceVehicle\PsvLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\LicenceVehicle\PsvLicenceVehicle as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Psv Licence Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvLicenceVehicleTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PsvLicenceVehicle();
        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(
            [
                'id' => 111
            ]
        );

        /** @var Entity\Licence\LicenceVehicle $licenceVehicle */
        $licenceVehicle = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $licenceVehicle->shouldReceive('serialize')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licenceVehicle);

        $result = $this->sut->handleQuery($query);

        $data = $result->serialize();

        $this->assertEquals(['foo' => 'bar'], $data);
    }
}
