<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\ConditionUndertaking\Get as QueryHandler;
use Dvsa\Olcs\Transfer\Query\ConditionUndertaking\Get as Qry;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as ConditionUndertakingRepo;

/**
 * GetTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('ConditionUndertaking', ConditionUndertakingRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $data = ['id' => 234];
        $query = Qry::create($data);

        $mockConditionUndertaking = m::mock(\Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking::class)
            ->shouldReceive('serialize')->with(
                [
                    'application' => ['licence'],
                    'licence',
                    'operatingCentre',
                ]
            )->once()->andReturn(['FooBar'])->getMock();

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchUsingId')->with($query)->once()
            ->andReturn($mockConditionUndertaking);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(['FooBar'], $result->serialize());
    }
}
