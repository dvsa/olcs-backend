<?php


namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\Surrender\OpenBusReg as QryHandler;
use Dvsa\Olcs\Api\Entity\View\BusRegSearchView;
use Dvsa\Olcs\Transfer\Query\Surrender\OpenBusReg;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class OpenBusRegTest extends QueryHandlerTestCase
{
    /**
     *
     */
    public function setUp()
    {
        $this->sut = new QryHandler();
        $this->mockRepo('BusRegSearchView', Cases::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = OpenBusReg::create(['licId' => 1]);

        $expected = [
            m::mock(BusRegSearchView::class)
                ->shouldReceive('serialize')->once()->andReturn('foo')->getMock()
        ];

        $this->repoMap['BusRegSearchView']->shouldReceive(
            'fetchActiveByLicence'
        )->andReturn($expected);


        $this->assertEquals([
            'count' => 1,
            'results' => ['foo']
        ], $this->sut->handleQuery($query));
    }
}
