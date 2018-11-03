<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\Surrender\Status;
use Dvsa\Olcs\Api\Domain\Repository\Surrender;
use Dvsa\Olcs\Transfer\Query\Surrender\GetStatus;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

class StatusTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Status();
        $this->mockRepo('Surrender', Surrender::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = GetStatus::create(['id' => 1]);
        $mockSurrender = \Mockery::mock(\Dvsa\Olcs\Api\Entity\Surrender::class);

        $mockSurrender->shouldReceive('serialize')->andReturn(["test"]);
        $this->repoMap['Surrender']->
        shouldReceive('fetchUsingId')->once()->andReturn($mockSurrender);


        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['test'], $result->serialize());
    }
}
