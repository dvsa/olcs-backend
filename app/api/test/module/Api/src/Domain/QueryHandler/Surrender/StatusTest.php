<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\Surrender\Status;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\Surrender\GetStatus;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class StatusTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Status();
        $this->mockRepo('Surrender', Surrender::class);
        $this->mockRepo('Licence', Licence::class);
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

        $mockLicence = m::mock(\Dvsa\Olcs\Api\Entity\Licence\Licence::class);
        $mockLicence->shouldReceive('getId')->once()->andReturn(1);
        $this->repoMap['Licence']->shouldReceive('fetchById')->once()
            ->andReturn($mockLicence);
        $this->repoMap['Surrender']->
        shouldReceive('fetchOneByLicence')->once()->andReturn($mockSurrender);


        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['test'], $result->serialize());
    }
}
