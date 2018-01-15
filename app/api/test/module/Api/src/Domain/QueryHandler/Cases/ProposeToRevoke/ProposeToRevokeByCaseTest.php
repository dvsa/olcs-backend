<?php

/**
 * ProposeToRevokeByCase Test
 */

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\ProposeToRevoke;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\Cases\ProposeToRevoke;
use Dvsa\Olcs\Api\Entity\System\SlaTargetDate;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ProposeToRevoke\ProposeToRevokeByCase;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\ProposeToRevoke as ProposeToRevokeRepo;
use Dvsa\Olcs\Transfer\Query\Cases\ProposeToRevoke\ProposeToRevokeByCase as Qry;

/**
 * ProposeToRevokeByCase Test
 */
class ProposeToRevokeByCaseTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ProposeToRevokeByCase();
        $this->mockRepo('ProposeToRevoke', ProposeToRevokeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['case' => 1]);

        $this->repoMap['ProposeToRevoke']
            ->shouldReceive('disableSoftDeleteable')
            ->with(
                [
                    \Dvsa\Olcs\Api\Entity\Pi\Reason::class
                ]
            )
            ->once();

        $proposeToRevokeEntity = m::mock(ProposeToRevoke::class);

        $this->repoMap['ProposeToRevoke']
            ->shouldReceive('fetchProposeToRevokeUsingCase')
            ->with($query)
            ->andReturn($proposeToRevokeEntity);

        $proposeToRevokeEntity
            ->shouldReceive('getSlaTargetDates')
            ->andReturn([
                $this->createSlaTargetDate('dummySlaProperty1', 'DUMMY-SLA-VAL-1'),
                $this->createSlaTargetDate('dummySlaProperty2', 'DUMMY-SLA-VAL-2'),
            ]);

        $proposeToRevokeEntity
            ->shouldReceive('serialize')
            ->with(['presidingTc', 'reasons', 'assignedCaseworker', 'actionToBeTaken'])
            ->andReturn(['foo']);

        /** @var Result $result */
        $result = $this->sut->handleQuery($query);
        $this->assertSame(
            [
                'foo',
                'dummySlaProperty1Target' => 'DUMMY-SLA-VAL-1',
                'dummySlaProperty2Target' => 'DUMMY-SLA-VAL-2',
            ],
            $result->serialize()
        );
    }

    protected function createSlaTargetDate($name, $targetDate)
    {
        $mock = m::mock(SlaTargetDate::class);
        $mock->shouldReceive('getSla->getField') ->andReturn($name);
        $mock->shouldReceive('getTargetDate') ->andReturn($targetDate);
        return $mock;
    }
}
