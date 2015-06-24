<?php

/**
 * ShortNoticeByBusReg Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\Bus\ShortNoticeByBusReg;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\BusShortNotice as ShortNoticeRepo;
use Dvsa\Olcs\Transfer\Query\Bus\ShortNoticeByBusReg as Qry;

/**
 * ShortNoticeByBusReg Test
 */
class ShortNoticeByBusRegTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ShortNoticeByBusReg();
        $this->mockRepo('BusShortNotice', ShortNoticeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['BusShortNotice']->shouldReceive('fetchByBusReg')
            ->with($query)
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query));
    }
}
