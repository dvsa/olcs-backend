<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\Surrender\GetStatus as QryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\Surrender\GetStatus;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class GetStatusTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QryHandler();
        $this->mockRepo('Surrender', Surrender::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = GetStatus::create(['id' => 1]);
        $mockSurrender = m::mock(\Dvsa\Olcs\Api\Entity\Surrender::class);
        $mockRefData = m::mock(RefData::class);
        $mockRefData->shouldReceive('serialize')->andReturn(["test"]);
        $mockSurrender->shouldReceive('getStatus')->andReturn(
            $mockRefData
        )->getMock();

        $this->repoMap['Surrender']->
        shouldReceive('fetchOneByLicence')->once()->with(1, 1)->andReturn($mockSurrender);


        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['test'], $result->serialize());
    }
}
