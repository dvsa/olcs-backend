<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\SelfserveIssuedPermitsSummary;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\SelfserveIssuedPermitsSummary as SelfserveIssuedPermitsSummaryQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

class SelfserveIssuedPermitsSummaryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SelfserveIssuedPermitsSummary();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $organisationId = 8;

        $rows = [
            'row1',
            'row2',
            'row3',
        ];

        $this->repoMap['IrhpApplication']->shouldReceive('fetchSelfserveIssuedPermitsSummary')
            ->with($organisationId)
            ->andReturn($rows);

        $result = $this->sut->handleQuery(SelfserveIssuedPermitsSummaryQry::create(['organisation' => $organisationId]));
        $this->assertEquals($rows, $result);
    }
}
