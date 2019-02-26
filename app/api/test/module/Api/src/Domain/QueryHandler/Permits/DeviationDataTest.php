<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\DeviationData;
use Dvsa\Olcs\Api\Domain\Query\Permits\DeviationData as DeviationDataQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class DeviationDataTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeviationData();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $sourceValues = [
            [
                'candidatePermitId' => 5,
                'applicationId' => 1,
                'licNo' => 123456,
                'permitsRequired' => 12
            ],
            [
                'candidatePermitId' => 8,
                'applicationId' => 2,
                'licNo' => 455123,
                'permitsRequired' => 6
            ],
            [
                'candidatePermitId' => 11,
                'applicationId' => 1,
                'licNo' => 123456,
                'permitsRequired' => 12
            ],
        ];

        $expectedDeviationData = [
            'licenceData' => [
                '123456' => [1 => '12'],
                '455123' => [2 => '6']
            ],
            'meanDeviation' => 1.5
        ];

        $result = $this->sut->handleQuery(
            DeviationDataQry::create(['sourceValues' => $sourceValues])
        );

        $this->assertEquals($expectedDeviationData, $result);
    }

    public function testNoSourceValuesNullMeanDeviation()
    {
        $sourceValues = [];

        $expectedDeviationData = [
            'licenceData' => [],
            'meanDeviation' => null
        ];

        $result = $this->sut->handleQuery(
            DeviationDataQry::create(['sourceValues' => $sourceValues])
        );

        $this->assertEquals($expectedDeviationData, $result);
    }
}
