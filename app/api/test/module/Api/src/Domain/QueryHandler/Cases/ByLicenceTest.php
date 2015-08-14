<?php

/**
 * ByLicence Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ByLicence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Transfer\Query\Cases\ByLicence as Qry;
use Mockery as m;

/**
 * ByLicence Test
 */
class ByLicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ByLicence();
        $this->mockRepo('Cases', CasesRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['Cases']->shouldReceive('fetchList')
            ->with($query, m::type('integer'))
            ->andReturn([$mockResult]);

        $this->repoMap['Cases']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
