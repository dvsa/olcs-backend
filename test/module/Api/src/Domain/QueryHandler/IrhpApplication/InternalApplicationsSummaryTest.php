<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\InternalApplicationsSummary;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\InternalApplicationsSummary as InternalApplicationsSummaryQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

class InternalApplicationsSummaryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new InternalApplicationsSummary();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $licenceId = 10;

        $rows = [
            'row1',
            'row2',
            'row3',
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchInternalApplicationsSummary')
            ->with($licenceId)
            ->andReturn($rows);

        $result = $this->sut->handleQuery(InternalApplicationsSummaryQry::create(['licence' => $licenceId]));
        $this->assertEquals($rows, $result);
    }
}
