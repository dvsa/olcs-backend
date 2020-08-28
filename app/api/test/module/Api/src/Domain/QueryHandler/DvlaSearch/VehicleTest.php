<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\DvlaSearch;

use Dvsa\Olcs\Api\Domain\QueryHandler\DvlaSearch\Vehicle;
use Dvsa\Olcs\DvlaSearch\Exception\VehicleUnavailableException;
use Dvsa\Olcs\DvlaSearch\Model\DvlaVehicle;
use Dvsa\Olcs\DvlaSearch\Service\Client;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class VehicleTest extends QueryHandlerTestCase
{
    protected $sut;

    public function setUp(): void
    {
        /**
         * @var Vehicle
         */
        $this->sut = new Vehicle();

        $this->mockedSmServices[Client::class] = m::mock(Client::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $queryData = ['vrm' => "ABC123"];

        $vehicle = m::mock(DvlaVehicle::class);
        $vehicle->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn([
                'registrationNumber' => $queryData['vrm']
            ]);
        $this->mockedSmServices[Client::class]->shouldReceive('getVehicle')
            ->with($queryData['vrm'])
            ->once()
            ->andReturn($vehicle);

        $query = (new \Dvsa\Olcs\Transfer\Query\DvlaSearch\Vehicle())->create($queryData);
        $actual = $this->sut->handleQuery($query);

        $expected = [
            'count' => 1,
            'result' => [
                0 => [
                    'registrationNumber' => $queryData['vrm']
                ]
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testNoVehicleReturnsEmptyResultSetHandleQuery()
    {
        $queryData = ['vrm' => "ABC123"];
        $this->mockedSmServices[Client::class]->shouldReceive('getVehicle')
            ->with($queryData['vrm'])
            ->once()
            ->andThrow(new VehicleUnavailableException());

        $query = (new \Dvsa\Olcs\Transfer\Query\DvlaSearch\Vehicle())->create($queryData);
        $actual = $this->sut->handleQuery($query);

        $expected = [
            'count' => 0,
            'result' => []
        ];
        $this->assertEquals($expected, $actual);
    }
}
