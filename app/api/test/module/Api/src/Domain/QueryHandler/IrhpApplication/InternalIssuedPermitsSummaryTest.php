<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\InternalIssuedPermitsSummary;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\InternalIssuedPermitsSummary as InternalIssuedPermitsSummaryQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

class InternalIssuedPermitsSummaryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new InternalIssuedPermitsSummary();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $licenceId = 14;

        $rows = [
            'row1',
            'row2',
            'row3',
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchInternalIssuedPermitsSummary')
            ->with($licenceId)
            ->andReturn($rows);

        $result = $this->sut->handleQuery(InternalIssuedPermitsSummaryQry::create(['licence' => $licenceId]));
        $this->assertEquals($rows, $result);
    }
}
