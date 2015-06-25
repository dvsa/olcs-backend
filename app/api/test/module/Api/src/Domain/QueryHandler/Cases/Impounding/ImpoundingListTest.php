<?php

/**
 * ImpoundingList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Impounding;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Impounding\ImpoundingList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Impounding as ImpoundingRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Impounding\ImpoundingList as Qry;
use Mockery as m;

/**
 * ImpoundingList Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ImpoundingListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ImpoundingList();
        $this->mockRepo('Impounding', ImpoundingRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn('foo');

        $this->repoMap['Impounding']->shouldReceive('fetchList')
            ->with($query, m::type('integer'))
            ->andReturn([$mockResult]);

        $this->repoMap['Impounding']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
