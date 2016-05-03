<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\ChecklistReminders
 */
class ChecklistRemindersTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler\ContinuationDetail\ChecklistReminders();

        $this->mockRepo('ContinuationDetail', Repository\ContinuationDetail::class);

        parent::setUp();
    }

    public function test()
    {
        $data = [
            'month' => 'unit_Month',
            'year' => 'unit_Year',
            'ids' => ['unit_Ids'],
        ];
        $query = Query\ContinuationDetail\ChecklistReminders::create($data);

        $expect = ['unit_RepoResult', null, null];

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchChecklistReminders')
            ->with('unit_Month', 'unit_Year', ['unit_Ids'])
            ->once()
            ->andReturn($expect);

        $actual = $this->sut->handleQuery($query);

        static::assertEquals(
            [
                'result' => $expect,
                'count' => 3,
            ],
            $actual
        );
    }
}
