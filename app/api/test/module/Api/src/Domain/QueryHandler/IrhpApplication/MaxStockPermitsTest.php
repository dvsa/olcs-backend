<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\MaxStockPermits;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\MaxStockPermits as MaxStockPermitsQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class MaxStockPermitsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new MaxStockPermits();

        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $licenceId = 8;
        $totAuthVehicles = 12;

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getTotAuthVehicles')
            ->withNoArgs()
            ->andReturn($totAuthVehicles);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($licence);

        $livePermitCounts = [
            [
                'irhpPermitStockId' => 5,
                'irhpPermitCount' => 14
            ],
            [
                'irhpPermitStockId' => 6,
                'irhpPermitCount' => 7,
            ],
            [
                'irhpPermitStockId' => 7,
                'irhpPermitCount' => 0
            ]
        ];

        $this->repoMap['IrhpPermit']->shouldReceive('getLivePermitCountsGroupedByStock')
            ->with($licenceId)
            ->andReturn($livePermitCounts);

        $expectedResult = [
            'result' => [
                5 => 0,
                6 => 5,
                7 => 12
            ]
        ];

        $result = $this->sut->handleQuery(MaxStockPermitsQry::create(['licence' => $licenceId]));
        $this->assertEquals($expectedResult, $result);
    }
}
