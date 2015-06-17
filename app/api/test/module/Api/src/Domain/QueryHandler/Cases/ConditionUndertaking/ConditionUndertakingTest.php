<?php

/**
 * ConditionUndertaking Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\ConditionUndertaking;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ConditionUndertaking\ConditionUndertaking;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as ConditionUndertakingRepo;
use Dvsa\Olcs\Transfer\Query\Cases\ConditionUndertaking\ConditionUndertaking as Qry;
use Mockery as m;

/**
 * ConditionUndertaking Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ConditionUndertakingTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ConditionUndertaking();
        $this->mockRepo('ConditionUndertaking', ConditionUndertakingRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);
        $mockResult = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchUsingCaseId')
            ->with($query)
            ->andReturn($mockResult);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\QueryHandler\Result',$result);
    }
}
